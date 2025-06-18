<?php

namespace App\Http\Controllers;

use App\Models\Substansi;
use Illuminate\Http\Request;

class SubstansiController extends Controller
{
    // Menampilkan daftar substansi
    public function index(Request $request)
    {
        $query = \App\Models\Substansi::query();

        // Pencarian
        if ($request->filled('search_substansi')) {
            $search = strtolower($request->input('search_substansi'));
            $query->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
        }

        // Sorting
        switch ($request->input('sort_by')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'nama':
                $query->orderBy('nama', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination
        $perPage = $request->input('perPage', 10);
        $substansis = $query->paginate($perPage);
        $substansis->appends($request->only(['perPage', 'search_substansi', 'sort_by']));

        return view('substansis.index', compact('substansis'));
    }

    // Menampilkan form tambah substansi
    public function create()
    {
        return view('substansis.create');
    }

    // Menyimpan data substansi baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:substansis',
        ]);

        Substansi::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('substansis.index')->with('success', 'Substansi berhasil ditambahkan.');
    }

    // Menampilkan form edit substansi
    public function edit(Substansi $substansi)
    {
        return view('substansis.edit', compact('substansi'));
    }

    // Memperbarui data substansi
    public function update(Request $request, Substansi $substansi)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:substansis,nama,' . $substansi->id,
        ]);

        $substansi->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('substansis.index')->with('success', 'Substansi berhasil diperbarui.');
    }

    // Menghapus substansi
    public function destroy(Substansi $substansi)
    {
        $substansi->delete();

        return redirect()->route('substansis.index')->with('success', 'Substansi berhasil dihapus.');
    }
}
