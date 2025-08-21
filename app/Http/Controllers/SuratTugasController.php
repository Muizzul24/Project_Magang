<?php

namespace App\Http\Controllers;

use App\Models\SuratTugas;
use App\Models\Substansi;
use App\Models\Pegawai;
use App\Models\DasarSurat;
use App\Models\ParafSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class SuratTugasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $searchTanggal = $request->input('search_tanggal');
        $searchTujuan = $request->input('search_tujuan');
        $searchSubstansi = $request->input('search_substansi');
        $searchPegawai = $request->input('search_pegawai');
        $sortBy = $request->input('sort_by', 'tanggal_terdekat');

        $query = SuratTugas::with(['pegawais', 'dasarSurat', 'parafSurat', 'penandatangan', 'substansi']);

        if (auth()->user()->role === 'operator') {
            $query->where('substansi_id', auth()->user()->substansi_id);
        }

        // Pencarian
        if ($searchTanggal) {
            try {
                // =============================================
                // PERBAIKAN: Konversi format tanggal sebelum query
                // =============================================
                $tanggalFormatted = Carbon::createFromFormat('d-m-Y', $searchTanggal)->format('Y-m-d');
                $query->whereDate('tanggal_surat', $tanggalFormatted);
            } catch (\Exception $e) {
                // Abaikan jika format tanggal salah, tidak perlu menghentikan proses
            }
        }

        if ($searchTujuan) {
            $query->whereRaw('LOWER(tujuan) LIKE ?', ['%' . strtolower($searchTujuan) . '%']);
        }

        if ($searchSubstansi) {
            $query->whereHas('substansi', function ($q) use ($searchSubstansi) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($searchSubstansi) . '%']);
            });
        }

        if ($searchPegawai) {
            $query->whereHas('pegawais', function ($q) use ($searchPegawai) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($searchPegawai) . '%']);
            });
        }

        // Sorting
        switch ($sortBy) {
            case 'tanggal_terdekat':
                $query->orderBy('tanggal_surat', 'asc');
                break;
            case 'tanggal_terjauh':
                $query->orderBy('tanggal_surat', 'desc');
                break;
            case 'substansi':
                $query->join('substansis', 'surat_tugas.substansi_id', '=', 'substansis.id')
                    ->orderBy('substansis.nama')
                    ->select('surat_tugas.*');
                break;
            case 'terbaru':
                $query->orderBy('surat_tugas.created_at', 'desc');
                break;
            default:
                $query->orderBy('tanggal_surat', 'asc');
                break;
        }

        $suratTugas = $query->paginate($perPage)->appends($request->except('page'));

        return view('surat_tugas.index', compact('suratTugas'));
    }

    public function create()
    {
        // Batasi substansi berdasarkan role
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        $substansiPenandatangan = Substansi::all();
        $dasarSurats = DasarSurat::all();
        $parafSurats = ParafSurat::all();

        return view('surat_tugas.create', compact('substansis', 'substansiPenandatangan', 'dasarSurats', 'parafSurats'));
    }

    public function getPegawaiBySubstansi($substansi_id)
    {
        $pegawais = Pegawai::where('substansi_id', $substansi_id)->get();
        return response()->json($pegawais);
    }

    public function getPenandatanganBySubstansi($id)
    {
        $pegawais = Pegawai::where('substansi_id', $id)->get();

        // Jika ingin filter berdasarkan jabatan:
        // ->where('jabatan', 'ILIKE', '%kepala%')

        return response()->json($pegawais);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'substansi_id' => 'required|exists:substansis,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'dasar_surat_id' => 'required|array|min:1',
            'dasar_surat_id.*' => 'exists:dasar_untuk_surat,id',
            'paraf_surat_id' => 'required|array|min:1',
            'paraf_surat_id.*' => 'exists:paraf_untuk_surat,id',
            'substansi_penandatangan_id' => 'required|exists:substansis,id',
            'penandatangan_id' => 'required|exists:pegawais,id',
            'substansi_penandatangan_id' => 'required|exists:substansis,id',
            'penandatangan_id' => 'required|exists:pegawais,id',
        ]);

        DB::transaction(function () use ($validated) {
            $suratTugas = SuratTugas::create([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'substansi_id' => $validated['substansi_id'],
                'substansi_penandatangan_id' => $validated['substansi_penandatangan_id'],
                'penandatangan_id' => $validated['penandatangan_id'],
            ]);

            $suratTugas->pegawais()->sync($validated['pegawai_ids']);
            $suratTugas->dasarSurat()->sync($validated['dasar_surat_id']);
            $suratTugas->parafSurat()->sync($validated['paraf_surat_id']);

            $this->regenerateDocument($suratTugas);
        });

        return redirect()->route('surat_tugas.index')->with('success', 'Surat Tugas berhasil dibuat!');
    }

    public function show($id)
    {
        $suratTugas = SuratTugas::with('pegawais', 'dasarSurat', 'parafSurat')->findOrFail($id);
        return view('surat_tugas.show', compact('suratTugas'));
    }

    public function edit($id)
    {
        $suratTugas = SuratTugas::with('pegawais', 'dasarSurat', 'parafSurat')->findOrFail($id);

        // Batasi substansi berdasarkan role
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        $dasarSurats = DasarSurat::all();
        $parafSurats = ParafSurat::all();

        return view('surat_tugas.edit', compact('suratTugas', 'substansis', 'dasarSurats', 'parafSurats'));
    }

    public function update(Request $request, $id)
    {
        $suratTugas = SuratTugas::findOrFail($id);

        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'tujuan' => 'required|string|max:255',
            'substansi_id' => 'required|exists:substansis,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'dasar_surat_id' => 'required|array|min:1',
            'dasar_surat_id.*' => 'exists:dasar_untuk_surat,id',
            'paraf_surat_id' => 'required|array|min:1',
            'paraf_surat_id.*' => 'exists:paraf_untuk_surat,id',
        ]);

        DB::transaction(function () use ($suratTugas, $validated) {
            $suratTugas->update([
                'nomor_surat' => $validated['nomor_surat'],
                'tanggal_surat' => $validated['tanggal_surat'],
                'tujuan' => $validated['tujuan'],
                'substansi_id' => $validated['substansi_id'],
            ]);

            $suratTugas->pegawais()->sync($validated['pegawai_ids']);
            $suratTugas->dasarSurat()->sync($validated['dasar_surat_id']);
            $suratTugas->parafSurat()->sync($validated['paraf_surat_id']);

            $this->regenerateDocument($suratTugas);
        });

        return redirect()->route('surat_tugas.index')->with('success', 'Surat Tugas berhasil diperbarui.');
    }

    private function regenerateDocument($suratTugas)
    {
        $templatePath = storage_path('app/public/SuratTugas/Surat_Tugas_Template.docx');
        if (file_exists($templatePath)) {
            $templateProcessor = new TemplateProcessor($templatePath);

            $suratTugas->load(['pegawais', 'dasarSurat', 'parafSurat', 'substansi']);

            $templateProcessor->setValue('nomor_surat', $suratTugas->nomor_surat);
            $templateProcessor->setValue('tujuan', $suratTugas->tujuan);
            $templateProcessor->setValue('tanggal', $this->formatTanggalIndonesia($suratTugas->tanggal_surat));
            $templateProcessor->setValue('substansi', $suratTugas->substansi->nama ?? '-');

            $parafValues = $this->formatParafSuratArray($suratTugas->parafSurat);
            $templateProcessor->cloneRowAndSetValues('paraf_surat', $parafValues);

            $dasarSuratValues = $this->formatDasarSuratArray($suratTugas->dasarSurat);
            $templateProcessor->cloneRowAndSetValues('no_d', $dasarSuratValues);

            $pegawaiValues = $this->formatPegawaiArray($suratTugas->pegawais);
            $templateProcessor->cloneRowAndSetValues('no_p', $this->formatPegawaiArray($suratTugas->pegawais));

            $penandatangan = Pegawai::find($suratTugas->penandatangan_id);

            if ($penandatangan) {
                $penandatanganData = $this->formatPenandatanganDetail($penandatangan);
                $templateProcessor->setValue('penandatangan_jabatan_atas', $penandatanganData['penandatangan_jabatan_atas']);
                $templateProcessor->setValue('penandatangan_nama', $penandatanganData['penandatangan_nama']);
                $templateProcessor->setValue('penandatangan_pangkat', $penandatanganData['penandatangan_pangkat']);
                $templateProcessor->setValue('penandatangan_nip', $penandatanganData['penandatangan_nip']);
            }

            $fileName = 'Surat_Tugas_' . $suratTugas->id . '.docx';
            $folderPath = storage_path('app/public/SuratTugas');
            if (!file_exists($folderPath)) mkdir($folderPath, 0755, true);
            $templateProcessor->saveAs($folderPath . '/' . $fileName);

            $suratTugas->surattugas = 'SuratTugas/' . $fileName;
            $suratTugas->save();
        }
    }

    private function formatPenandatanganDetail(Pegawai $penandatangan)
    {
        return [
            'penandatangan_jabatan_atas' => strtoupper($penandatangan->jabatan), // e.g. KEPALA DINAS ...
            'penandatangan_nama'         => $penandatangan->nama,
            'penandatangan_pangkat'      => $penandatangan->pangkat_golongan,
            'penandatangan_nip'          => 'NIP ' . $penandatangan->nip,
        ];
    }

    private function formatPegawaiArray($pegawais)
    {
        $result = [];

        foreach ($pegawais as $i => $pegawai) {
            $result[] = [
                'kepada' => $i === 0 ? 'Kepada :' : '',
                'no_p' => $i + 1,
                'pegawai_detail' =>"Nama                          : {$pegawai->nama}\nNIP                              : {$pegawai->nip}\nPangkat/Golongan      : {$pegawai->pangkat_golongan}\nJabatan                       : {$pegawai->jabatan}\n"
            ];
        }

        return $result;
    }

    private function formatDasarSuratArray($dasarSurats)
    {
        $result = [];

        foreach ($dasarSurats as $i => $dasar) {
            $result[] = [
                'dasar' => $i === 0 ? 'Dasar    :' : '',  // hanya baris pertama ada label
                'no_d' => $i + 1 . '.',
                'dasar_surat' => $dasar->dasar_surat
            ];
        }

        return $result;
    }

    private function formatTanggalIndonesia($tanggal)
    {
        $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $date = Carbon::parse($tanggal);
        return $date->day . ' ' . $bulan[$date->month - 1] . ' ' . $date->year;
    }

    private function formatParafSuratArray($parafSurats)
    {
        $result = [];
        foreach ($parafSurats as $i => $paraf) {
            $result[] = [
                'paraf_surat' => ' ' . $paraf->paraf_surat
            ];
        }
        return $result;
    }

    public function destroy($id)
    {
        $suratTugas = SuratTugas::findOrFail($id);

        // =============================================
        // PERBAIKAN: Cek relasi ke Agenda sebelum hapus
        // =============================================
        if ($suratTugas->agendas()->exists()) {
            return redirect()->route('surat_tugas.index')
                ->with('error', 'Gagal! Surat tugas ini tidak bisa dihapus karena masih digunakan oleh sebuah agenda.');
        }

        try {
            DB::transaction(function () use ($suratTugas) {
                $suratTugas->pegawais()->detach();
                $suratTugas->dasarSurat()->detach();
                $suratTugas->parafSurat()->detach();

                if ($suratTugas->surattugas) {
                    $path = storage_path('app/public/' . $suratTugas->surattugas);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                
                $suratTugas->delete();
            });

            return redirect()->route('surat_tugas.index')->with('success', 'Surat Tugas berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $suratTugas = SuratTugas::findOrFail($id);
        $path = storage_path('app/public/' . $suratTugas->surattugas);
        if (!$suratTugas->surattugas || !file_exists($path)) {
            return back()->withErrors(['error' => 'File surat tugas tidak ditemukan.']);
        }
        return response()->download($path, 'Surat_Tugas_' . $suratTugas->nomor_surat . '.docx');
    }
}
