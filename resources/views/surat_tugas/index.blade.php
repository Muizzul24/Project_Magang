@extends('layouts.app')

@section('title', 'Surat Tugas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold">Daftar Surat Tugas</h2>
</div>

<a href="{{ route('surat_tugas.create') }}"
   class="bg-blue-500 text-white font-bold px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">
    Tambah Surat Tugas
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

    <div class="flex flex-wrap gap-2 items-center">
        <input type="text" name="search_tujuan" placeholder="Cari Tujuan" value="{{ request('search_tujuan') }}" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="text" id="search_tanggal" name="search_tanggal" value="{{ request('search_tanggal') }}" class="border border-gray-300 rounded px-3 py-1" placeholder="Cari Tanggal">
        <input type="text" name="search_substansi" placeholder="Cari Substansi" value="{{ request('search_substansi') }}" class="border border-gray-300 rounded px-3 py-1">
        <input type="text" name="search_pegawai" placeholder="Cari Pegawai" value="{{ request('search_pegawai') }}" class="border border-gray-300 rounded px-3 py-1">

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded">Cari</button>
        <a href="{{ route('surat_tugas.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

        <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1">
            <option value="">Urutkan Berdasarkan</option>
            <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Data Terbaru</option>
            <option value="tanggal_terdekat" {{ request('sort_by') == 'tanggal_terdekat' ? 'selected' : '' }}>Tanggal Terdekat</option>
            <option value="tanggal_terjauh" {{ request('sort_by') == 'tanggal_terjauh' ? 'selected' : '' }}>Tanggal Terjauh</option>
            <option value="substansi" {{ request('sort_by') == 'substansi' ? 'selected' : '' }}>Substansi</option>
        </select>

    </div>
</form>

@if (session('success'))
    <div class="auto-dismiss bg-green-200 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="auto-dismiss bg-red-200 text-red-800 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

{{-- Tabel --}}
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="py-2 px-4 text-center">No</th>
                <th class="py-2 px-4 text-center">Nomor Surat</th>
                <th class="py-2 px-4 text-center">Tanggal Surat</th>
                <th class="py-2 px-4 text-center">Tujuan</th>
                <th class="py-2 px-4 text-center">Substansi</th>
                <th class="py-2 px-4 text-center">Pegawai</th>
                <th class="py-2 px-4 text-center">Dasar Surat</th>
                <th class="py-2 px-4 text-center">Paraf Surat</th>
                <th class="py-2 px-4 text-center">Penandatangan</th>
                <th class="py-2 px-4 text-center">Surat Tugas</th>
                <th class="py-2 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratTugas as $surat)
                <tr class="hover:bg-gray-50 border-b text-center">
                    <td class="py-2 px-4">{{ ($suratTugas->currentPage() - 1) * $suratTugas->perPage() + $loop->iteration }}</td>
                    <td class="py-2 px-4">{{ $surat->nomor_surat }}</td>
                    <td class="py-2 px-4">{{ $surat->tanggal_surat->isoFormat('D MMMM Y') }}</td>
                    <td class="py-2 px-4 text-left">{{ $surat->tujuan }}</td>
                    <td class="py-2 px-4">{{ $surat->substansi->nama ?? '-' }}</td>
                    <td class="py-2 px-4">
                        @foreach ($surat->pegawais as $pegawai)
                            <span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-1 mb-1">{{ $pegawai->nama }}</span>
                        @endforeach
                    </td>
                    <td class="py-2 px-4 text-sm text-left">
                        @foreach ($surat->dasarSurat as $dasar)
                            <div class="bg-gray-100 text-xs text-gray-800 border border-gray-300 rounded px-2 py-1 mb-1">{{ $dasar->dasar_surat }}</div>
                        @endforeach
                    </td>
                    <td class="py-2 px-4 text-sm text-left">
                        @foreach ($surat->parafSurat as $paraf)
                            <div class="bg-gray-100 text-xs text-gray-800 border border-gray-300 rounded px-2 py-1 mb-1">{{ $paraf->paraf_surat }}</div>
                        @endforeach
                    </td>
                    <td class="py-2 px-4 text-sm">
                        @if ($surat->penandatangan)
                            <div class="bg-gray-100 text-xs text-gray-800 border border-gray-300 rounded px-2 py-1">{{ $surat->penandatangan->nama }}</div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        @if($surat->surattugas)
                            <a href="{{ asset('storage/' . $surat->surattugas) }}" target="_blank" class="text-green-600 hover:text-green-800 underline">Lihat</a>
                        @else
                            <span class="text-gray-400">Tidak ada</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex flex-col items-center space-y-1">
                            <a href="{{ route('surat_tugas.show', $surat->id) }}" class="w-20 bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-sm text-center">Detail</a>
                            @if(auth()->user()->role !== 'anggota')
                                <a href="{{ route('surat_tugas.edit', $surat->id) }}" class="w-20 bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-sm text-center">Edit</a>
                                <button type="button"
                                        class="w-20 bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-sm text-center open-delete"
                                        data-action="{{ route('surat_tugas.destroy', $surat->id) }}">
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="py-4 text-center text-gray-500">Tidak ada data surat tugas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $suratTugas->appends(request()->except('page'))->links() }}
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="confirm-delete" class="hidden fixed z-50 top-24 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus surat tugas ini?</p>
    <form method="POST" id="delete-form">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-2">
            <button type="button" class="cancel-delete px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Inisialisasi Flatpickr ---
        flatpickr("#search_tanggal", {
            dateFormat: "d-m-Y",
        });

        // --- Logika untuk Modal Hapus ---
        const modal = document.getElementById('confirm-delete');
        if (modal) {
            const form = document.getElementById('delete-form');
            const openButtons = document.querySelectorAll('.open-delete');
            const cancelButtons = modal.querySelectorAll('.cancel-delete');

            openButtons.forEach(button => {
                button.addEventListener('click', () => {
                    form.setAttribute('action', button.getAttribute('data-action'));
                    modal.classList.remove('hidden');
                });
            });

            cancelButtons.forEach(button => {
                button.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            });
        }

        // --- Logika untuk Notifikasi Auto-Dismiss ---
        const alerts = document.querySelectorAll('.auto-dismiss');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                setTimeout(function() {
                    alert.remove();
                }, 500);

            }, 5000); // 5 detik
        });
    });
</script>
@endpush
