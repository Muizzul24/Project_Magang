@extends('layouts.app')

@section('title', 'Data Agenda')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Daftar Agenda</h2>
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'operator']))
        <a href="{{ route('agendas.create') }}" class="bg-blue-500 text-white font-bold px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">Tambah Agenda</a>
    @endif

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
                name="search_kegiatan" 
                placeholder="Cari Kegiatan" 
                value="{{ request('search_kegiatan') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <input 
                type="text" 
                name="search_asal_surat" 
                placeholder="Cari Asal Surat" 
                value="{{ request('search_asal_surat') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            {{-- Input tanggal mulai --}}
            <input 
                type="date" 
                name="search_tanggal_mulai" 
                value="{{ request('search_tanggal_mulai') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Tanggal Mulai"
            >
            {{-- Input tanggal akhir --}}
            <input 
                type="date" 
                name="search_tanggal_akhir" 
                value="{{ request('search_tanggal_akhir') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Tanggal Akhir"
            >
             <input 
                type="text" 
                name="search_tempat" 
                placeholder="Cari Tempat" 
                value="{{ request('search_tempat') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <input 
                type="text" 
                name="search_substansi" 
                placeholder="Cari Substansi" 
                value="{{ request('search_substansi') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <input 
                type="text" 
                name="search_pegawai" 
                placeholder="Cari Pegawai" 
                value="{{ request('search_pegawai') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded"
            >
                Cari
            </button>
            <a href="{{ route('agendas.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Urutkan Berdasarkan</option>
                <option value="tanggal_terdekat" {{ request('sort_by') == 'tanggal_terdekat' ? 'selected' : '' }}>Tanggal Terdekat</option>
                <option value="tanggal_terjauh" {{ request('sort_by') == 'tanggal_terjauh' ? 'selected' : '' }}>Tanggal Terjauh</option>
                <option value="asal_surat" {{ request('sort_by') == 'asal_surat' ? 'selected' : '' }}>Asal Surat</option>
                <option value="tempat" {{ request('sort_by') == 'tempat' ? 'selected' : '' }}>Tempat</option>
                <option value="substansi" {{ request('sort_by') == 'substansi' ? 'selected' : '' }}>Substansi</option>
            </select>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b text-center">No</th>
                    <th class="py-2 px-4 border-b text-center">Kegiatan</th>
                    <th class="py-2 px-4 border-b text-center">Asal Surat</th>
                    <th class="py-2 px-4 border-b text-center">Tanggal</th>
                    <th class="py-2 px-4 border-b text-center">Tempat</th>
                    <th class="py-2 px-4 border-b text-center">Substansi</th>
                    <th class="py-2 px-4 border-b text-center">Nama Pegawai</th>
                    <th class="py-2 px-4 border-b text-center">Keterangan</th>
                    <th class="py-2 px-4 border-b text-center">Surat</th>
                    <th class="py-2 px-4 border-b text-center">Surat Tugas</th>
                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($agendas as $agenda)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b text-center">
                        {{ ($agendas->currentPage() - 1) * $agendas->perPage() + $loop->iteration }}
                    </td>
                    <td class="py-2 px-4 border-b">{{ $agenda->kegiatan }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $agenda->asal_surat }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d-m Y') }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $agenda->tempat }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        {{ $agenda->substansi->nama ?? '-' }}
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        @foreach ($agenda->pegawais as $pegawai)
                            <span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-1 mb-1">
                                {{ $pegawai->nama }}
                            </span>
                        @endforeach
                    </td>
                    <td class="py-2 px-4 border-b">{{ $agenda->keterangan_agenda }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        @if($agenda->surat)
                            @php
                                $files = explode(',', $agenda->surat);
                            @endphp
                            <div class="space-y-1">
                                @foreach($files as $index => $file)
                                    @php $fileName = basename($file); @endphp
                                    <div>
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank"
                                        class="text-blue-500 hover:text-blue-700 underline">
                                            Lihat Surat {{ $index + 1 }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">Tidak ada</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        @if($agenda->surat_tugas && file_exists(public_path('storage/' . $agenda->surat_tugas)))
                            <a href="{{ asset('storage/' . $agenda->surat_tugas) }}" target="_blank" class="text-green-600 hover:text-green-800 underline">
                                Lihat Surat Tugas
                            </a>
                        @else
                            <span class="text-gray-400">Tidak ada</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 border-b text-center">
                        <div class="flex flex-col items-center space-y-1">
                            {{-- Tombol Detail --}}
                            <a href="{{ route('agendas.show', $agenda->id) }}"
                            class="w-20 bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-sm text-center">
                                Detail
                            </a>

                            @if(in_array(auth()->user()->role, ['admin', 'operator']))
                                {{-- Tombol Edit --}}
                                <a href="{{ route('agendas.edit', $agenda->id) }}"
                                class="w-20 bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-sm text-center">
                                    Edit
                                </a>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('agendas.destroy', $agenda->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-20 bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-sm text-center">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-gray-500">Tidak ada agenda.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $agendas->appends(request()->except('page'))->links() }}
    </div>
@endsection