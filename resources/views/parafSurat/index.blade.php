@extends('layouts.app')

@section('title', 'Paraf Surat')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Daftar Paraf Surat</h1>

    <!-- Tombol Tambah Paraf Surat -->
    <a href="{{ route('parafSurat.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Tambah Paraf Surat
    </a>

    <!-- Filter Jumlah Tampil & Pencarian -->
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-4">
        <div>
            <label for="perPage" class="mr-2 font-medium">Tampilkan:</label>
            <select name="perPage" id="perPage" onchange="this.form.submit()" class="border-gray-300 rounded px-2 py-1">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <span class="ml-2 text-sm text-gray-500">Data Satu Halaman</span>
        </div>

        <!-- Pencarian -->
        <div class="flex flex-wrap gap-2 items-center">
            <input 
                type="text" 
                name="search" 
                placeholder="Cari Paraf Surat" 
                value="{{ request('search') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded"
            >
                Cari
            </button>
            <a href="{{ route('parafSurat.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <!-- Sorting -->
            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Urutkan Berdasarkan</option>
                <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Inputan Terbaru</option>
                <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Inputan Terlama</option>
            </select>
        </div>
    </form>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Data -->
    @if($parafSurats->count())
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b text-left">Paraf Surat</th>
                        <th class="py-2 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($parafSurats as $index => $parafSurat)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $parafSurats->firstItem() + $index }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $parafSurat->paraf_surat }}</td>
                            <td class="py-2 px-4 border-b">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('parafSurat.edit', $parafSurat->id) }}"
                                       class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded text-sm font-bold">Edit</a>
                                    <form action="{{ route('parafSurat.destroy', $parafSurat->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded text-sm font-bold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            {{ $parafSurats->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-600 mt-4">Data paraf surat belum tersedia.</p>
    @endif
</div>
@endsection
