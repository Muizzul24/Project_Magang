<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Substansi;  // Import model Substansion
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $query = User::with('substansi');

        // Pencarian
        if ($request->filled('search_nama')) {
            $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->input('search_nama')) . '%']);
        }

        if ($request->filled('search_username')) {
            $query->whereRaw('LOWER(username) LIKE ?', ['%' . strtolower($request->input('search_username')) . '%']);
        }

        if ($request->filled('search_role')) {
            $query->whereRaw('LOWER(role) LIKE ?', ['%' . strtolower($request->input('search_role')) . '%']);
        }

        if ($request->filled('search_substansi')) {
            $query->whereHas('substansi', function ($q) use ($request) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->input('search_substansi')) . '%']);
            });
        }

        // Sorting
        switch ($request->input('sort_by')) {
            case 'substansi':
                $query->join('substansis', 'users.substansi_id', '=', 'substansis.id')
                    ->orderBy('substansis.nama')
                    ->select('users.*');
                break;

            case 'role':
                $query->orderBy('role');
                break;

            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ambil hasil dengan pagination
        $users = $query->paginate($perPage);
        $users->appends($request->except('page')); // Jaga parameter query string tetap aktif di pagination

        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Ambil semua substansi yang ada di database
        $substansis = Substansi::all();
        return view('users.create', compact('substansis'));  // Kirim substansi ke form create
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'role' => 'required|in:admin,operator,anggota',
            'password' => 'required|string|min:6|confirmed',
            'substansi_id' => 'required|exists:substansis,id',  // Validasi substansi yang dipilih
        ]);

        // Membuat user baru
        User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'substansi_id' => $request->substansi_id,  // Menyimpan substansi yang dipilih
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $substansis = Substansi::all();  // Ambil semua substansi untuk dropdown
        return view('users.edit', compact('user', 'substansis'));  // Menampilkan form edit dengan data substansi
    }

    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,  // Pastikan username tetap unik
            'password' => 'nullable|string|min:5',  // Password opsional
            'role' => 'required|string',
            'substansi_id' => 'required|exists:substansis,id',  // Validasi substansi yang dipilih
        ]);

        // Update data pengguna
        $user->nama = $request->nama;
        $user->username = $request->username;
        if ($request->password) {
            $user->password = Hash::make($request->password);  // Enkripsi password jika diubah
        }
        $user->role = $request->role;
        $user->substansi_id = $request->substansi_id;  // Update substansi
        $user->save();  // Simpan perubahan

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();  // Hapus user dari database
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
