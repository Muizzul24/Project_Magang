<?php

namespace App\Http\Controllers;
use App\Models\Agenda;
use App\Models\Pegawai;
use App\Models\Substansi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AgendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,operator')->only(['create', 'store', 'edit', 'update', 'destroy', 'arsip']);
        $this->middleware('role:admin,operator,anggota')->only(['index', 'show']);
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
        $perPage = $request->input('perPage', 10); // default 10 data per halaman

        $agendas = Agenda::with(['substansi', 'pegawais'])
            ->where('arsip', false)
            ->whereDate('tanggal', '>=', now()->toDateString());

        // 🔒 Batasi hanya untuk substansi milik operator
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

        if ($request->filled('search_tanggal_mulai') && $request->filled('search_tanggal_akhir')) {
            $agendas->whereBetween('tanggal', [
                $request->input('search_tanggal_mulai'),
                $request->input('search_tanggal_akhir')
            ]);
        }

        // Sorting
        switch ($request->input('sort_by')) {
            case 'asal_surat':
                $agendas->orderBy('asal_surat');
                break;
            case 'tempat':
                $agendas->orderBy('tempat');
                break;
            case 'tanggal_terjauh':
                $agendas->orderBy('tanggal', 'desc');
                break;
            case 'substansi':
                $agendas->join('substansis', 'agendas.substansi_id', '=', 'substansis.id')
                        ->orderBy('substansis.id')
                        ->select('agendas.*');
                break;
            default:
                $agendas->orderBy('tanggal', 'asc');
                break;
        }

        $agendas = $agendas->paginate($perPage);
        $agendas->appends($request->all());

        // Batasi dropdown substansi juga (untuk filter UI)
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        return view('agendas.index', compact('agendas', 'substansis'));
    }
    
    public function create()
    {
        // Ambil semua substansi sesuai role
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        // Ambil semua pegawai
        $allPegawais = Pegawai::all();

        // Ambil semua surat tugas yang sudah ada, diurutkan berdasarkan tanggal terbaru
        $suratTugas = \App\Models\SuratTugas::orderBy('tanggal_surat', 'desc')->get();

        // Kirim semua data ke view
        return view('agendas.create', compact('substansis', 'allPegawais', 'suratTugas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'substansi_id' => 'required|exists:substansis,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'kegiatan' => 'required|string|max:255',
            'asal_surat' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tempat' => 'required|string|max:255',
            'keterangan_agenda' => 'required|string',
            'surat.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
            'surat_tugas_id' => 'required|exists:surat_tugas,id',
        ]);

        // Ambil semua data kecuali file dan pegawai
        $data = $request->except(['surat', 'pegawai_ids']);

        // Simpan data agenda terlebih dahulu agar dapat ID-nya
        $agenda = \App\Models\Agenda::create($data);

        // Hubungkan pegawai yang dipilih
        $agenda->pegawais()->attach($request->pegawai_ids);

        // Handle upload multiple file surat (jika ada)
        $suratPaths = [];

        if ($request->hasFile('surat')) {
            foreach ($request->file('surat') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newName = $originalName . '_' . $agenda->id . '.' . $extension;
                $path = $file->storeAs('surat', $newName, 'public');
                $suratPaths[] = $path;
            }

            // Simpan path surat-surat tersebut sebagai string (dipisahkan koma)
            $agenda->surat = implode(',', $suratPaths);
            $agenda->save();
        }

        // Ambil dan salin file surat tugas yang dipilih
        $selectedSurat = \App\Models\SuratTugas::find($request->surat_tugas_id);

        if ($selectedSurat && $selectedSurat->surattugas) {
            $originalPath = storage_path('app/public/' . $selectedSurat->surattugas);
            $fileExtension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFileName = 'Agenda_SuratTugas_' . $agenda->id . '.' . $fileExtension;
            $destinationPath = storage_path('app/public/surat_tugas_agenda');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $copied = copy($originalPath, $destinationPath . '/' . $newFileName);

            if ($copied) {
                $agenda->surat_tugas = 'surat_tugas_agenda/' . $newFileName;
                $agenda->save();
            }
        }

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil ditambahkan dan surat tugas dilampirkan.');
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

        // 🔒 Batasi pilihan substansi hanya milik operator
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
            'tanggal' => 'required|date',
            'tempat' => 'required|string|max:255',
            'keterangan_agenda' => 'required|string',
            'surat.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
            'surat_tugas_id' => 'required|exists:surat_tugas,id'
        ]);

        $agenda->update([
            'substansi_id' => $request->substansi_id,
            'kegiatan' => $request->kegiatan,
            'asal_surat' => $request->asal_surat,
            'tanggal' => $request->tanggal,
            'tempat' => $request->tempat,
            'keterangan_agenda' => $request->keterangan_agenda,
        ]);

        if ($request->hasFile('surat')) {
            $suratPaths = [];
            foreach ($request->file('surat') as $file) {
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileNameFinal = $filename . '_' . $agenda->id . '.' . $extension;
                $path = $file->storeAs('surat', $fileNameFinal, 'public');
                $suratPaths[] = $path;
            }
            
            // Jika ada file lama, gabungkan dengan file baru
            $existingFiles = $agenda->surat ? explode(',', $agenda->surat) : [];
            $allFiles = array_merge($existingFiles, $suratPaths);
            
            $agenda->surat = implode(',', $allFiles);
            $agenda->save();
        }

        $agenda->pegawais()->sync($request->pegawai_ids ?? []);

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil diperbarui!');
    }
    
    public function arsipAgendaTerlewat()
    {
        $agendaTerlewat = Agenda::where('tanggal', '<', today())->where('arsip', false)->get();

        foreach ($agendaTerlewat as $agenda) {
            $agenda->arsip = true;
            $agenda->save();
        }

        return redirect()->route('agendas.arsip')->with('success', 'Agenda yang terlewat telah dipindahkan ke arsip.');
    }

    public function arsip()
    {
        $agendaArsip = Agenda::where('arsip', true)
            ->when(auth()->user()->role === 'operator', function ($query) {
                $query->where('substansi_id', auth()->user()->substansi_id);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('agendas.arsip', compact('agendaArsip'));
    }

 /**
 * Hapus file surat individual
 */
    public function deleteSurat(Agenda $agenda, $index)
    {
        try {
            // Cek authorization
            if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $agenda->substansi_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tidak diizinkan menghapus file ini.'
                ], 403);
            }

            // Validasi apakah ada file surat
            if (!$agenda->surat) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tidak ada file surat yang ditemukan.'
                ], 404);
            }

            $files = explode(',', $agenda->surat);
            
            // Validasi index
            if (!isset($files[$index])) {
                return response()->json([
                    'success' => false,
                    'error' => 'File tidak ditemukan pada index tersebut.'
                ], 404);
            }

            $fileToDelete = $files[$index];
            
            // Hapus file fisik dari storage
            $fileDeleted = false;
            if (Storage::disk('public')->exists($fileToDelete)) {
                $fileDeleted = Storage::disk('public')->delete($fileToDelete);
            }

            // Hapus dari array dan update database
            unset($files[$index]);
            $files = array_values($files); // Re-index array
            
            // Update kolom surat di database
            $agenda->surat = empty($files) ? null : implode(',', $files);
            $dbUpdated = $agenda->save();

            if ($dbUpdated) {
                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil dihapus.',
                    'remaining_files' => $files,
                    'file_deleted_from_storage' => $fileDeleted,
                    'total_remaining' => count($files)
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'error' => 'Gagal mengupdate database.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error("Error deleting file: " . $e->getMessage(), [
                'agenda_id' => $agenda->id,
                'file_index' => $index,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Agenda $agenda)
    {
        // Hapus relasi pegawai
        $agenda->pegawais()->detach();

        // Hapus file surat jika ada
        if ($agenda->surat) {
            $files = explode(',', $agenda->surat);
            foreach ($files as $file) {
                $filePath = 'public/' . $file;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        }

        // Hapus file surat tugas jika ada
        if ($agenda->surat_tugas && Storage::disk('public')->exists($agenda->surat_tugas)) {
            Storage::disk('public')->delete($agenda->surat_tugas);
        }

        $agenda->delete();

        return redirect()->route('agendas.index')->with('success', 'Agenda berhasil dihapus.');
    }
}