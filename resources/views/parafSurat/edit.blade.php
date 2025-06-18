@extends('layouts.app')

@section('title', 'Edit Paraf Surat')

@section('content')
<div class="container mx-auto p-4 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Edit Paraf Surat</h1>

    @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('parafSurat.update', $parafSurat->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="paraf_surat" class="block font-medium text-sm text-gray-700 mb-1">Paraf Surat</label>
        <textarea name="paraf_surat" id="paraf_surat" rows="4" class="border rounded w-full p-2" required>{{ old('paraf_surat', $parafSurat->paraf_surat) }}</textarea>

        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded mt-4">Update</button>
        <a href="{{ route('parafSurat.index') }}" class="ml-4 text-gray-700 hover:underline">Batal</a>
    </form>
</div>
@endsection
