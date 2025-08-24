<?php

namespace App\Http\Controllers;

use App\Models\DasarSurat;
use Illuminate\Http\Request;

class DasarSuratController extends Controller
{
    // Menampilkan daftar semua dasar surat
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'terbaru');

        $query = DasarSurat::query();

        if ($search) {
            $query->whereRaw('LOWER(dasar_surat) LIKE ?', ['%' . strtolower($search) . '%']);
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

        $dasarSurats = $query->paginate($perPage)->appends($request->all());

        return view('dasarSurat.index', compact('dasarSurats'));
    }

    // Menampilkan form tambah dasar surat baru
    public function create()
    {
        return view('dasarSurat.create');
    }

    // Menyimpan data dasar surat baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'dasar_surat' => 'required|string',
        ]);

        DasarSurat::create([
            'dasar_surat' => $request->dasar_surat,
        ]);

        return redirect()->route('dasarSurat.index')->with('success', 'Dasar Surat berhasil dibuat.');
    }

    // Menampilkan form edit dasar surat
    public function edit(DasarSurat $dasarSurat)
    {
        return view('dasarSurat.edit', compact('dasarSurat'));
    }

    // Memperbarui data dasar surat di database
    public function update(Request $request, DasarSurat $dasarSurat)
    {
        $request->validate([
            'dasar_surat' => 'required|string',
        ]);

        $dasarSurat->update([
            'dasar_surat' => $request->dasar_surat,
        ]);

        return redirect()->route('dasarSurat.index')->with('success', 'Dasar Surat berhasil diperbarui.');
    }

    // Menghapus data dasar surat
    public function destroy(DasarSurat $dasarSurat)
    {
        // 1. Cek apakah dasar surat ini masih digunakan di surat tugas
        if ($dasarSurat->suratTugas()->exists()) {
            return redirect()->route('dasarSurat.index')
                ->with('error', 'Gagal! Dasar surat tidak bisa dihapus karena masih digunakan oleh surat tugas lain.');
        }

        // 2. Jika tidak ada relasi, baru hapus data
        $dasarSurat->delete();

        return redirect()->route('dasarSurat.index')->with('success', 'Dasar Surat berhasil dihapus.');
    }
}
