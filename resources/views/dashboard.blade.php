@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Dashboard</h1>

    {{-- MENU UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

        <a href="{{ route('dashboard') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">📊 Dashboard</h2>
            <p class="text-sm text-gray-500">Lihat ringkasan aktivitas</p>
        </a>

        <a href="{{ route('agendas.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">📅 Data Agenda</h2>
            <p class="text-sm text-gray-500">Kelola agenda kegiatan</p>
        </a>

        <a href="{{ route('agendas.arsip') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-yellow-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-yellow-600">📁 Arsip Agenda</h2>
            <p class="text-sm text-gray-500">Agenda yang sudah berlalu</p>
        </a>

        <a href="{{ route('pegawais.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">👥 Data Pegawai</h2>
            <p class="text-sm text-gray-500">Kelola daftar pegawai</p>
        </a>

        <a href="{{ route('dasarSurat.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">📄 Dasar Surat</h2>
            <p class="text-sm text-gray-500">Kelola referensi dasar surat</p>
        </a>

        <a href="{{ route('parafSurat.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">✍️ Paraf Surat</h2>
            <p class="text-sm text-gray-500">Kelola data paraf</p>
        </a>

        <a href="{{ route('surat_tugas.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">📬 Surat Tugas</h2>
            <p class="text-sm text-gray-500">Manajemen surat tugas</p>
        </a>

        <a href="{{ route('users.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">👤 Daftar User</h2>
            <p class="text-sm text-gray-500">Kelola akun pengguna</p>
        </a>

        <a href="{{ route('substansis.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">📌 Daftar Substansi</h2>
            <p class="text-sm text-gray-500">Manajemen unit substansi</p>
        </a>

        <a href="{{ route('kalender.index') }}" class="p-4 bg-white shadow-md rounded-lg hover:bg-blue-50 border border-gray-200">
            <h2 class="text-lg font-semibold text-blue-600">🗓️ Kalender</h2>
            <p class="text-sm text-gray-500">Lihat jadwal kegiatan</p>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="p-4 bg-white shadow-md rounded-lg border border-gray-200 hover:bg-red-50">
            @csrf
            <button type="submit" class="text-left w-full">
                <h2 class="text-lg font-semibold text-red-600">🚪 Logout</h2>
                <p class="text-sm text-gray-500">Keluar dari sistem</p>
            </button>
        </form>
    </div>

    {{-- FILTER DAN TABEL AGENDA TERBARU --}}
    <div class="mt-10">
        <form method="GET" class="flex items-center gap-4 mb-4">
            <label for="periode" class="font-medium text-gray-700">Tampilkan Agenda dari:</label>
            <select name="periode" id="periode" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1">
                <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>1 Minggu Terakhir</option>
                <option value="14" {{ request('periode') == '14' ? 'selected' : '' }}>2 Minggu Terakhir</option>
                <option value="30" {{ request('periode') == '30' ? 'selected' : '' }}>1 Bulan Terakhir</option>
            </select>
        </form>

        @if($recentAgendas->count())
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Kegiatan</th>
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Tempat</th>
                            <th class="px-4 py-2 border">Substansi</th>
                            <th class="px-4 py-2 border">Pegawai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAgendas as $agenda)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border text-center">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border">{{ $agenda->kegiatan }}</td>
                                <td class="px-4 py-2 border text-center">{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d-m-Y') }}</td>
                                <td class="px-4 py-2 border text-center">{{ $agenda->tempat }}</td>
                                <td class="px-4 py-2 border text-center">{{ $agenda->substansi->nama ?? '-' }}</td>
                                <td class="px-4 py-2 border text-center">
                                    @foreach ($agenda->pegawais as $pegawai)
                                        <span class="inline-block text-sm text-gray-700 bg-gray-200 rounded px-2 py-1 mr-1 mb-1">
                                            {{ $pegawai->nama }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">Tidak ada agenda dalam rentang waktu yang dipilih.</p>
        @endif
    </div>
</div>
@endsection
