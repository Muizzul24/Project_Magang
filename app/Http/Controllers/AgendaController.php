<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Pegawai;
use App\Models\Substansi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Pastikan Validator di-import

class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,operator')->only(['create', 'store', 'edit', 'update', 'destroy', 'arsipAgendaTerlewat', 'deleteSurat']);
        $this->middleware('role:admin,operator,anggota')->only(['index', 'show', 'arsip']);
    }

    public function getPegawaiBySubstansi($substansi_id)
    {
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $substansi_id) {
            return response()->json([], 403);
        }

        $pegawais = Pegawai::where('substansi_id', $substansi_id)->get();
        return response()->json($pegawais);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        // --- PERUBAHAN: Query menggunakan tanggal_selesai ---
        $agendas = Agenda::with(['substansi', 'pegawais'])
            ->where('arsip', false)
            ->whereDate('tanggal_selesai', '>=', now()->toDateString()); // Agenda aktif selama belum melewati tanggal selesai

        if (in_array(auth()->user()->role, ['operator', 'anggota'])) {
            $agendas->where('substansi_id', auth()->user()->substansi_id);
        }

        // Pencarian kolom
        if ($request->filled('search_kegiatan')) {
            $agendas->whereRaw('LOWER(kegiatan) LIKE ?', ['%' . strtolower($request->input('search_kegiatan')) . '%']);
        }
        if ($request->filled('search_asal_surat')) {
            $agendas->whereRaw('LOWER(asal_surat) LIKE ?', ['%' . strtolower($request->input('search_asal_surat')) . '%']);
        }
        if ($request->filled('search_tempat')) {
            $agendas->whereRaw('LOWER(tempat) LIKE ?', ['%' . strtolower($request->input('search_tempat')) . '%']);
        }
        if ($request->filled('search_substansi')) {
            $agendas->whereHas('substansi', function ($q) use ($request) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->input('search_substansi')) . '%']);
            });
        }
        if ($request->filled('search_pegawai')) {
            $agendas->whereHas('pegawais', function ($q) use ($request) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->input('search_pegawai')) . '%']);
            });
        }

        // --- PERUBAHAN: Pencarian rentang tanggal ---
        if ($request->filled('search_tanggal_mulai') && $request->filled('search_tanggal_akhir')) {
            $searchMulai = Carbon::parse($request->input('search_tanggal_mulai'))->format('Y-m-d');
            $searchSelesai = Carbon::parse($request->input('search_tanggal_akhir'))->format('Y-m-d');
            
            // Mencari agenda yang rentangnya bersinggungan dengan rentang pencarian
            $agendas->where(function ($query) use ($searchMulai, $searchSelesai) {
                $query->where('tanggal_mulai', '<=', $searchSelesai)
                      ->where('tanggal_selesai', '>=', $searchMulai);
            });
        }

        // --- PERUBAHAN: Sorting berdasarkan tanggal_mulai ---
        switch ($request->input('sort_by')) {
            case 'asal_surat':
                $agendas->orderBy('asal_surat');
                break;
            case 'tempat':
                $agendas->orderBy('tempat');
                break;
            case 'tanggal_terjauh':
                $agendas->orderBy('tanggal_mulai', 'desc'); // Diurutkan dari tanggal mulai
                break;
            case 'substansi':
                $agendas->join('substansis', 'agendas.substansi_id', '=', 'substansis.id')
                        ->orderBy('substansis.id')
                        ->select('agendas.*');
                break;
            case 'terbaru':
                $agendas->orderBy('agendas.created_at', 'desc');
                break;
            case 'terlama':
                $agendas->orderBy('agendas.created_at', 'asc');
                break;
            default:
                $agendas->orderBy('tanggal_mulai', 'asc'); // Default urut dari tanggal mulai
                break;
        }

        $agendas = $agendas->paginate($perPage);
        $agendas->appends($request->all());

        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        return view('agendas.index', compact('agendas', 'substansis'));
    }
    
    public function create()
    {
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }
        $allPegawais = Pegawai::all();
        $suratTugas = \App\Models\SuratTugas::orderBy('tanggal_surat', 'desc')->get();
        return view('agendas.create', compact('substansis', 'allPegawais', 'suratTugas'));
    }

    public function store(Request $request)
    {
        // --- PERUBAHAN: Validasi untuk tanggal_mulai dan tanggal_selesai ---
        $request->validate([
            'substansi_id' => 'required|exists:substansis,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'kegiatan' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date_format:d-m-Y',
            'tanggal_selesai' => 'required|date_format:d-m-Y|after_or_equal:tanggal_mulai',
            'tempat' => 'required|string|max:255',
            'keterangan_agenda' => 'required|string',
            'surat.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5000',
            'surat_tugas_id' => 'nullable|exists:surat_tugas,id',
        ]);

        // --- PERUBAHAN: Konversi dan siapkan data tanggal ---
        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');

        $data = $request->except(['surat', 'pegawai_ids', 'tanggal_mulai', 'tanggal_selesai']);
        $data['tanggal_mulai'] = $tanggalMulai;
        $data['tanggal_selesai'] = $tanggalSelesai;

        $agenda = Agenda::create($data);
        $agenda->pegawais()->attach($request->pegawai_ids);

        // Upload file (logika ini tidak berubah)
        $suratPaths = [];
        if ($request->hasFile('surat')) {
            foreach ($request->file('surat') as $file) {
                if ($file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $newName = $originalName . '_' . $agenda->id . '.' . $extension;
                    $path = $file->storeAs('surat', $newName, 'public');
                    $suratPaths[] = $path;
                }
            }
            $agenda->surat = implode(',', $suratPaths);
            $agenda->save();
        }

        // Duplikat file surat tugas (logika ini tidak berubah)
        $selectedSurat = \App\Models\SuratTugas::find($request->surat_tugas_id);
        if ($selectedSurat && $selectedSurat->surattugas) {
            $originalPath = storage_path('app/public/' . $selectedSurat->surattugas);
            if (file_exists($originalPath)) {
                $fileExtension = pathinfo($originalPath, PATHINFO_EXTENSION);
                $newFileName = 'Agenda_SuratTugas_' . $agenda->id . '.' . $fileExtension;
                $destinationPath = storage_path('app/public/surat_tugas_agenda');
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                if (copy($originalPath, $destinationPath . '/' . $newFileName)) {
                    $agenda->surat_tugas = 'surat_tugas_agenda/' . $newFileName;
                    $agenda->save();
                }
            }
        }

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil ditambahkan.');
    }

    public function show(Agenda $agenda)
    {
        return view('agendas.show', compact('agenda'));
    }

    public function edit(Agenda $agenda)
    {
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $agenda->substansi_id) {
            return redirect()->route('agendas.index')->with('error', 'Anda hanya dapat mengedit agenda di substansi Anda sendiri.');
        }
        $agenda->load('pegawais');
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }
        $allPegawais = Pegawai::where('substansi_id', $agenda->substansi_id)->get();
        $suratTugas = \App\Models\SuratTugas::orderBy('tanggal_surat', 'desc')->get();
        return view('agendas.edit', compact('agenda', 'substansis', 'allPegawais', 'suratTugas'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $agenda->substansi_id) {
            return redirect()->route('agendas.index')->with('error', 'Tidak diizinkan.');
        }

        $request->validate([
            'substansi_id' => 'required|exists:substansis,id',
            'pegawai_ids' => 'nullable|array',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'kegiatan' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date_format:d-m-Y',
            'tanggal_selesai' => 'required|date_format:d-m-Y|after_or_equal:tanggal_mulai',
            'tempat' => 'required|string|max:255',
            'keterangan_agenda' => 'required|string',
            'surat.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5000',
            'surat_tugas_id' => 'nullable|exists:surat_tugas,id'
        ]);

        $tanggalMulai = Carbon::createFromFormat('d-m-Y', $request->tanggal_mulai)->format('Y-m-d');
        $tanggalSelesai = Carbon::createFromFormat('d-m-Y', $request->tanggal_selesai)->format('Y-m-d');

        $agenda->update([
            'substansi_id' => $request->substansi_id,
            'kegiatan' => $request->kegiatan,
            'asal_surat' => $request->asal_surat,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'tempat' => $request->tempat,
            'keterangan_agenda' => $request->keterangan_agenda,
            'surat_tugas_id' => $request->surat_tugas_id,
        ]);

        // Upload file surat pengantar (jika ada)
        if ($request->hasFile('surat')) {
            $suratPaths = [];
            foreach ($request->file('surat') as $file) {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileNameFinal = $filename . '_' . $agenda->id . '.' . $extension;
                $path = $file->storeAs('surat', $fileNameFinal, 'public');
                $suratPaths[] = $path;
            }
            $existingFiles = $agenda->surat ? explode(',', $agenda->surat) : [];
            $allFiles = array_merge($existingFiles, $suratPaths);
            $agenda->surat = implode(',', $allFiles);
            $agenda->save();
        }

        if ($request->filled('surat_tugas_id')) {
            // Hapus file lama jika ada
            if ($agenda->surat_tugas && \Illuminate\Support\Facades\Storage::disk('public')->exists($agenda->surat_tugas)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($agenda->surat_tugas);
            }

            // Duplikat file baru
            $selectedSurat = \App\Models\SuratTugas::find($request->surat_tugas_id);
            if ($selectedSurat && $selectedSurat->surattugas) {
                $originalPath = storage_path('app/public/' . $selectedSurat->surattugas);
                if (file_exists($originalPath)) {
                    $fileExtension = pathinfo($originalPath, PATHINFO_EXTENSION);
                    $newFileName = 'Agenda_SuratTugas_' . $agenda->id . '.' . $fileExtension;
                    $newPath = 'surat_tugas_agenda/' . $newFileName;
                    
                    \Illuminate\Support\Facades\Storage::disk('public')->copy($selectedSurat->surattugas, $newPath);
                    
                    $agenda->surat_tugas = $newPath;
                    $agenda->save();
                }
            }
        } else {
            // Jika input surat tugas dikosongkan, hapus file lama
            if ($agenda->surat_tugas && \Illuminate\Support\Facades\Storage::disk('public')->exists($agenda->surat_tugas)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($agenda->surat_tugas);
            }
            $agenda->surat_tugas = null;
            $agenda->save();
        }

        $agenda->pegawais()->sync($request->pegawai_ids ?? []);

        $redirectRoute = $request->input('from') === 'arsip' 
            ? 'agendas.arsip' 
            : 'agendas.index';

        return redirect()->route($redirectRoute)->with('success', 'Agenda berhasil diperbarui!');
    }
    
    public function arsip(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('perPage', 10);

        // Mulai query dengan kondisi dasar (hanya data yang diarsip)
        $query = Agenda::where('arsip', true);

        // Batasi data untuk operator/anggota
        $query->when(in_array($user->role, ['operator', 'anggota']), function ($q) use ($user) {
            $q->where('substansi_id', $user->substansi_id);
        });

        if ($request->filled('search_kegiatan')) {
            // Gunakan ILIKE untuk pencarian case-insensitive di PostgreSQL
            $query->where('kegiatan', 'ILIKE', '%' . $request->input('search_kegiatan') . '%');
        }
        
        // Pencarian berdasarkan nama pegawai
        if ($request->filled('search_pegawai')) {
            $query->whereHas('pegawais', function ($q) use ($request) {
                $q->where('nama', 'ILIKE', '%' . $request->input('search_pegawai') . '%');
            });
        }

        // Pencarian berdasarkan rentang tanggal
        if ($request->filled('search_tanggal_mulai') && $request->filled('search_tanggal_akhir')) {
            try {
                // Konversi format tanggal dari DD-MM-YYYY ke YYYY-MM-DD
                $searchMulai = Carbon::createFromFormat('d-m-Y', $request->input('search_tanggal_mulai'))->startOfDay();
                $searchSelesai = Carbon::createFromFormat('d-m-Y', $request->input('search_tanggal_akhir'))->endOfDay();
                
                // Cari agenda yang rentangnya bersinggungan dengan rentang pencarian
                $query->where(function ($q) use ($searchMulai, $searchSelesai) {
                    $q->where('tanggal_mulai', '<=', $searchSelesai)
                    ->where('tanggal_selesai', '>=', $searchMulai);
                });
            } catch (\Exception $e) {
                // Abaikan jika format tanggal yang dimasukkan salah
            }
        }

        // Urutkan data berdasarkan tanggal selesai yang paling baru
        $query->orderBy('tanggal_selesai', 'desc');

        // Ambil data dengan paginasi dan lampirkan parameter pencarian
        $agendaArsip = $query->paginate($perPage)->appends($request->except('page'));

        return view('agendas.arsip', compact('agendaArsip'));
    }

    public function arsipAgendaTerlewat()
    {
        // --- PERUBAHAN: Arsipkan agenda yang tanggal selesainya sudah lewat ---
        $updated = Agenda::where('tanggal_selesai', '<', today())
            ->where('arsip', false)
            ->update(['arsip' => true]);

        return redirect()->route('agendas.arsip')->with(
            'success',
            $updated > 0 ? 'Agenda yang terlewat telah dipindahkan ke arsip.' : 'Tidak ada agenda yang perlu diarsipkan.'
        );
    }

    public function deleteSurat(Request $request, Agenda $agenda)
    {
        // Validasi bahwa nama file dikirim
        $validated = $request->validate(['filename' => 'required|string']);
        $fileToDelete = $validated['filename'];

        try {
            if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $agenda->substansi_id) {
                return response()->json(['success' => false, 'error' => 'Tidak diizinkan.'], 403);
            }

            if (!$agenda->surat) {
                return response()->json(['success' => false, 'error' => 'Tidak ada file surat.'], 404);
            }

            $files = explode(',', $agenda->surat);

            // Cari nama file di dalam array
            $key = array_search($fileToDelete, $files);

            // Jika file tidak ditemukan di dalam daftar
            if ($key === false) {
                return response()->json(['success' => false, 'error' => 'File yang akan dihapus tidak ditemukan dalam daftar.'], 404);
            }

            // Hapus file dari storage
            if (Storage::disk('public')->exists($fileToDelete)) {
                Storage::disk('public')->delete($fileToDelete);
            }

            // Hapus file dari array
            unset($files[$key]);

            // Simpan kembali daftar file yang sudah diperbarui
            $agenda->surat = empty($files) ? null : implode(',', array_values($files));
            $agenda->save();

            return response()->json(['success' => true, 'message' => 'File berhasil dihapus.']);

        } catch (\Exception $e) {
            \Log::error("Error deleting file: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function destroy(Request $request, Agenda $agenda) // Tambahkan Request
    {
        $agenda->pegawais()->detach();
        if ($agenda->surat) {
            $files = explode(',', $agenda->surat);
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
        if ($agenda->surat_tugas && Storage::disk('public')->exists($agenda->surat_tugas)) {
            Storage::disk('public')->delete($agenda->surat_tugas);
        }
        $agenda->delete();

        $redirectRoute = $request->input('from') === 'arsip' 
            ? 'agendas.arsip' 
            : 'agendas.index';

        return redirect()->route($redirectRoute)->with('success', 'Agenda berhasil dihapus.');
    }
}
