@extends('layouts.app')

@section('title', 'Edit Data Pegawai')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-6">Edit Pegawai</h2>

    <form action="{{ route('pegawais.update', $pegawai->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama" class="block text-sm font-bold text-gray-700 mb-2">Nama:</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $pegawai->nama) }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <div class="mb-4">
            <label for="nip" class="block text-sm font-bold text-gray-700 mb-2">NIP:</label>
            <input type="text" id="nip" name="nip" value="{{ old('nip', $pegawai->nip) }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <div class="mb-4">
            <label for="pangkat_golongan" class="block text-sm font-bold text-gray-700 mb-2">Pangkat/Golongan:</label>
            <input type="text" id="pangkat_golongan" name="pangkat_golongan" value="{{ old('pangkat_golongan', $pegawai->pangkat_golongan) }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <div class="mb-4">
            <label for="jabatan" class="block text-sm font-bold text-gray-700 mb-2">Jabatan:</label>
            <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}"
                   class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
        </div>

        <div class="mb-6">
            <label for="substansi_id" class="block text-sm font-bold text-gray-700 mb-2">Substansi:</label>
            <select id="substansi_id" name="substansi_id"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" required>
                <option value="">Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ $pegawai->substansi_id == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center justify-start">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Simpan Perubahan
            </button>
            <a href="{{ route('pegawais.index') }}"
               class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
