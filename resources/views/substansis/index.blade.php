@extends('layouts.app')

@section('title', 'Daftar Substansi')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Daftar Substansi</h1>

    <!-- Tombol Tambah -->
    <a href="{{ route('substansis.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Tambah Substansi
    </a>

    <!-- Filter Jumlah & Pencarian -->
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

        <div class="flex flex-wrap gap-2 items-center">
            <input 
                type="text" 
                name="search_substansi" 
                placeholder="Cari Substansi" 
                value="{{ request('search_substansi') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded"
            >
                Cari
            </button>

            <a href="{{ route('substansis.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Urutkan Berdasarkan</option>
                <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Inputan Terbaru</option>
                <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Inputan Paling Awal</option>
                <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama A - Z</option>
            </select>
        </div>
    </form>

    <!-- Sukses Message -->
    @if (session('success'))
        <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Substansi -->
    @if ($substansis->count() > 0)
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b">Nama Substansi</th>
                        <th class="py-2 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($substansis as $index => $substansi)
                        <tr class="hover:bg-gray-50 text-center">
                            <td class="py-2 px-4 border-b">{{ ($substansis->currentPage() - 1) * $substansis->perPage() + $loop->iteration }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $substansi->nama }}</td>
                            <td class="py-2 px-4 border-b space-x-1">
                                <a href="{{ route('substansis.edit', $substansi) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-sm">Edit</a>

                                <form action="{{ route('substansis.destroy', $substansi) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus substansi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            {{ $substansis->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-600 mt-4">Tidak ada data substansi yang tersedia.</p>
    @endif
</div>
@endsection
