<?php

namespace App\Http\Controllers;

use App\Models\ParafSurat;
use Illuminate\Http\Request;

class ParafSuratController extends Controller
{
    // Menampilkan daftar data
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'terbaru');

        $query = ParafSurat::query();

        if ($search) {
            $query->whereRaw('LOWER(paraf_surat) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        switch ($sortBy) {
            case 'terbaru':
                $query->orderBy('created_at', 'asc');
                break;
            case 'terlama':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'asc');
                break;
        }

        $parafSurats = $query->paginate($perPage)->appends($request->all());

        return view('parafSurat.index', compact('parafSurats'));
    }

    // Menampilkan form tambah data
    public function create()
    {
        return view('parafSurat.create');
    }

    // Menyimpan data baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'paraf_surat' => 'required|string',
        ]);

        ParafSurat::create([
            'paraf_surat' => $request->paraf_surat,
        ]);

        return redirect()->route('parafSurat.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Menampilkan form edit data
    public function edit(ParafSurat $parafSurat)
    {
        return view('parafSurat.edit', compact('parafSurat'));
    }

    // Mengupdate data di database
    public function update(Request $request, ParafSurat $parafSurat)
    {
        $request->validate([
            'paraf_surat' => 'required|string',
        ]);

        $parafSurat->update([
            'paraf_surat' => $request->paraf_surat,
        ]);

        return redirect()->route('parafSurat.index')
                         ->with('success', 'Data berhasil diupdate.');
    }

    // Menghapus data
// app/Http/Controllers/ParafSuratController.php

    public function destroy(ParafSurat $parafSurat)
    {
        // 1. Cek apakah paraf surat ini masih digunakan di surat tugas
        if ($parafSurat->suratTugas()->exists()) {
            return redirect()->route('parafSurat.index')
                ->with('error', 'Gagal! Paraf surat tidak bisa dihapus karena masih digunakan oleh surat tugas lain.');
        }

        // 2. Jika tidak ada relasi, baru hapus data
        $parafSurat->delete();

        return redirect()->route('parafSurat.index')
                        ->with('success', 'Data berhasil dihapus.');
    }
}
