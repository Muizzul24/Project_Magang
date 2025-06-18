@extends('layouts.app')

@section('title', 'Tambah Paraf Surat')

@section('content')
<div class="container mx-auto p-6 max-w-md bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-extrabold mb-6 text-gray-800">Tambah Paraf Surat</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('parafSurat.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="paraf_surat" class="block text-sm font-semibold text-gray-700 mb-2">Paraf Surat</label>
            <textarea 
                name="paraf_surat" 
                id="paraf_surat" 
                rows="5" 
                class="w-full border border-gray-300 rounded-md p-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 resize-none"
                placeholder="Masukkan paraf surat di sini...">{{ old('paraf_surat') }}</textarea>
        </div>

        <div class="flex items-center space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md shadow-md transition duration-200">
                Simpan
            </button>
            <a href="{{ route('parafSurat.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">Batal</a>
            </a>
        </div>
    </form>
</div>
@endsection
