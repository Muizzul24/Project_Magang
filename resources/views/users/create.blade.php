@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6 bg-white shadow-md rounded">
    <h1 class="text-2xl font-bold mb-4">Buat Akun Baru</h1>

    @if (session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/users') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="nama" class="block font-medium">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="form-control w-full mt-1 border rounded p-2" value="{{ old('nama') }}" required>
        </div>

        <div class="mb-4">
            <label for="username" class="block font-medium">Username</label>
            <input type="text" name="username" id="username" class="form-control w-full mt-1 border rounded p-2" value="{{ old('username') }}" required>
        </div>

        <div class="mb-4">
            <label for="role" class="block font-medium">Role</label>
            <select name="role" id="role" class="form-control w-full mt-1 border rounded p-2" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                <option value="anggota" {{ old('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="substansi_id" class="block font-medium">Substansi</label>
            <select name="substansi_id" id="substansi_id" class="form-control w-full mt-1 border rounded p-2" required>
                <option value="">-- Pilih Substansi --</option>
                @foreach($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_id') == $substansi->id ? 'selected' : '' }}>{{ $substansi->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="password" class="block font-medium">Password</label>
            <input type="password" name="password" id="password" class="form-control w-full mt-1 border rounded p-2" required>
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control w-full mt-1 border rounded p-2" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Anggota</button>
            <a href="{{ route('users.index') }}"
        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Batal</a>
    </form>
</div>
@endsection
