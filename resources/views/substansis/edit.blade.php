@extends('layouts.app')

@section('title', 'Edit Substansi')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow mt-8">
    <h2 class="text-2xl font-bold mb-6">Edit Substansi</h2>

    <form action="{{ route('substansis.update', $substansi) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Substansi:</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $substansi->nama) }}" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            @error('nama')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('substansis.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Batal</a>
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection
