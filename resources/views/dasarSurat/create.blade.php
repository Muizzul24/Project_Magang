@extends('layouts.app')

@section('title', 'Tambah Dasar Surat')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Tambah Dasar Surat</h1>

    @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dasarSurat.store') }}" method="POST">
        @csrf

        <label for="dasar_surat" class="block font-medium text-sm text-gray-700 mb-1">Dasar Surat</label>
        <textarea name="dasar_surat" id="dasar_surat" rows="4" class="border rounded w-full p-2">{{ old('dasar_surat') }}</textarea>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Simpan</button>
        <a href="{{ route('dasarSurat.index') }}"
            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Batal</a>
    </form>
</div>
@endsection
