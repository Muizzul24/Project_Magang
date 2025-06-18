@extends('layouts.app')

@section('title', 'Tambah Data Pegawai')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-6">Tambah Pegawai</h2>

    <form action="{{ route('pegawais.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        <!-- Substansi -->
        <div class="mb-4">
            <label for="substansi_id" class="block text-sm font-bold text-gray-700 mb-2">Substansi:</label>
            <select id="substansi_id" name="substansi_id"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
                <option value="">Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_id') == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
            @error('substansi_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama -->
        <div class="mb-4">
            <label for="nama" class="block text-sm font-bold text-gray-700 mb-2">Nama:</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            @error('nama')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- NIP -->
        <div class="mb-4">
            <label for="nip" class="block text-sm font-bold text-gray-700 mb-2">NIP:</label>
            <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            @error('nip')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pangkat/Golongan -->
        <div class="mb-4">
            <label for="pangkat_golongan" class="block text-sm font-bold text-gray-700 mb-2">Pangkat/Golongan:</label>
            <input type="text" id="pangkat_golongan" name="pangkat_golongan" value="{{ old('pangkat_golongan') }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            @error('pangkat_golongan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jabatan -->
        <div class="mb-4">
            <label for="jabatan" class="block text-sm font-bold text-gray-700 mb-2">Jabatan:</label>
            <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan') }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
            @error('jabatan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan</button>
            <a href="{{ route('pegawais.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Batal</a>
        </div>
    </form>
</div>
@endsection
