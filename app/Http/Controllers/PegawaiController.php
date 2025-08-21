<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Substansi;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        // Inisialisasi query awal
        $query = \App\Models\Pegawai::query()->with('substansi')
            ->join('substansis', 'pegawais.substansi_id', '=', 'substansis.id')
            ->select('pegawais.*');

        // Filtering untuk role operator
        if (auth()->user()->role === 'operator') {
            $query->where('pegawais.substansi_id', auth()->user()->substansi_id);
        }

        // Filter berdasarkan nama substansi
        if ($request->filled('search_substansi')) {
            $query->whereHas('substansi', function ($q) use ($request) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->input('search_substansi')) . '%']);
            });
        }

        // Filter berdasarkan nama pegawai
        if ($request->filled('search_pegawai')) {
            $searchTerm = strtolower($request->input('search_pegawai'));
            $query->whereRaw('LOWER(pegawais.nama) LIKE ?', ["%{$searchTerm}%"]);
        }

        // Sorting berdasarkan permintaan
        switch ($request->input('sort_by')) {
            case 'nama':
                $query->orderBy('pegawais.nama');
                break;
            case 'nip':
                $query->orderBy('pegawais.nip');
                break;
            case 'pangkat_golongan':
                $query->orderBy('pegawais.pangkat_golongan');
                break;
            case 'jabatan':
                $query->orderBy('pegawais.jabatan');
                break;
            case 'substansi':
                $query->orderBy('substansis.id');
                break;
            case 'terbaru':
                $query->orderBy('pegawais.created_at', 'desc');
                break;
            case 'terlama':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('substansis.id')->orderBy('pegawais.updated_at', 'desc');
                break;
        }

        // Pagination
        $perPage = $request->input('perPage', 10);
        $pegawais = $query->paginate($perPage);

        // Keep filters and sort on pagination links
        $pegawais->appends($request->only([
            'perPage', 'search_substansi', 'search_pegawai', 'sort_by'
        ]));

        return view('pegawais.index', compact('pegawais'));
    }

    public function create()
    {
        if (auth()->user()->role === 'operator') {
            $substansis = Substansi::where('id', auth()->user()->substansi_id)->get();
        } else {
            $substansis = Substansi::all();
        }

        return view('pegawais.create', compact('substansis'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'substansi_id' => 'required|exists:substansis,id', // Pastikan substansi_id valid
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);

        // Cek jika operator yang login, hanya bisa menambah pegawai di substansinya sendiri
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id != $request->substansi_id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menambahkan pegawai di substansi Anda sendiri.');
        }

        // Simpan pegawai baru
        Pegawai::create([
            'substansi_id' => $request->substansi_id,
            'nama' => $request->nama,
            'nip' => $request->nip,
            'pangkat_golongan' => $request->pangkat_golongan,
            'jabatan' => $request->jabatan,
        ]);

        return redirect('/pegawais')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        return view('pegawais.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        // Batasi jika operator mencoba mengedit pegawai dari substansi lain
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id !== $pegawai->substansi_id) {
            return redirect()->route('pegawais.index')->with('error', 'Anda hanya dapat mengedit pegawai di substansi Anda sendiri.');
        }

        $substansis = auth()->user()->role === 'operator'
            ? Substansi::where('id', auth()->user()->substansi_id)->get()
            : Substansi::all();

        return view('pegawais.edit', compact('pegawai', 'substansis'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        // Cegah update jika bukan dari substansi yang sesuai
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id !== $pegawai->substansi_id) {
            return redirect()->route('pegawais.index')->with('error', 'Tidak diizinkan.');
        }

        $request->validate([
            'substansi_id' => 'required|exists:substansis,id',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);

        $pegawai->update($request->all());

        return redirect('/pegawais')->with('success', 'Pegawai berhasil diupdate.');
    }

    public function destroy(Pegawai $pegawai)
    {
        // 1. Cek otorisasi (kode Anda yang sudah ada)
        if (auth()->user()->role === 'operator' && auth()->user()->substansi_id !== $pegawai->substansi_id) {
            return redirect()->route('pegawais.index')->with('error', 'Tidak diizinkan.');
        }

        // =============================================
        // PERBAIKAN: Tambahkan logika pengecekan relasi
        // =============================================

        // 2. Cek apakah pegawai terdaftar sebagai penandatangan surat tugas
        if ($pegawai->suratTugasDitandatangani()->exists()) {
            return redirect()->route('pegawais.index')
                ->with('error', 'Gagal! Pegawai "' . $pegawai->nama . '" tidak bisa dihapus karena masih terdaftar sebagai penandatangan surat tugas.');
        }

        // 3. Cek apakah pegawai terdaftar di agenda
        if ($pegawai->agendas()->exists()) {
            return redirect()->route('pegawais.index')
                ->with('error', 'Gagal! Pegawai "' . $pegawai->nama . '" tidak bisa dihapus karena masih terdaftar dalam agenda.');
        }

        // 4. Jika semua pengecekan lolos, baru hapus pegawai
        $pegawai->delete();

        return redirect()->route('pegawais.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
