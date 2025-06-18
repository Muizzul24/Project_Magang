@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
    <div class="container mx-auto p-4">

        <h1 class="text-2xl font-semibold mb-4">Daftar Pegawai</h1>

        <!-- Tambah Pegawai Button -->
        <a href="{{ route('pegawais.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
            Tambah Pegawai
        </a>

    {{-- Filter Jumlah Tampil Per Halaman dan Pencarian --}}
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

        {{-- Pencarian per kolom --}}
        <div class="flex flex-wrap gap-2 items-center">
            <input 
                type="text" 
                name="search_pegawai" 
                placeholder="Cari Pegawai" 
                value="{{ request('search_pegawai') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
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
            <a href="{{ route('pegawais.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Urutkan Berdasarkan</option>
                <option value="substansi" {{ request('sort_by') == 'substansi' ? 'selected' : '' }}>Substansi</option>
                <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                <option value="nip" {{ request('sort_by') == 'nip' ? 'selected' : '' }}>NIP</option>
                <option value="pangkat_golongan" {{ request('sort_by') == 'pangkat_golongan' ? 'selected' : '' }}>Pangkat/Golongan</option>
                <option value="jabatan" {{ request('sort_by') == 'jabatan' ? 'selected' : '' }}>Jabatan</option>
                <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Inputan Terbaru</option>
                <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Inputan Terlama</option>
            </select>
        </div>
    </form>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel Daftar Pegawai -->
        @if ($pegawais->count() > 0)
            <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-2 px-4 border-b">Nama</th>
                            <th class="py-2 px-4 border-b">NIP</th>
                            <th class="py-2 px-4 border-b">Pangkat/Golongan</th>
                            <th class="py-2 px-4 border-b">Jabatan</th>
                            <th class="py-2 px-4 border-b">Substansi</th>
                            <th class="py-2 px-4 border-b">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawais as $pegawai)
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="py-2 px-4 border-b">{{ $pegawai->nama }}</td>
                                <td class="py-2 px-4 border-b">{{ $pegawai->nip }}</td>
                                <td class="py-2 px-4 border-b">{{ $pegawai->pangkat_golongan }}</td>
                                <td class="py-2 px-4 border-b">{{ $pegawai->jabatan }}</td>
                                <td class="py-2 px-4 border-b">{{ $pegawai->substansi->nama }}</td>
                                <td class="py-2 px-4 border-b text-center">
                                    @if(auth()->user()->role === 'admin' || auth()->user()->substansi_id === $pegawai->substansi_id)
                                        <a href="{{ route('pegawais.edit', $pegawai->id) }}"
                                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded mr-2 inline-block">Edit</a>

                                        <form action="{{ route('pegawais.destroy', $pegawai->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center">
                {{ $pegawais->links('pagination::tailwind') }}
            </div>
        @else
            <p class="text-gray-600">Tidak ada data pegawai yang tersedia.</p>
        @endif

    </div>
@endsection
