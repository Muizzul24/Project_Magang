@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Daftar Pegawai</h1>

    <!-- Tambah Pegawai Button -->
    <a href="{{ route('pegawais.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Tambah Pegawai
    </a>

    {{-- Filter --}}
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
            <input type="text" name="search_pegawai" placeholder="Cari Pegawai" value="{{ request('search_pegawai') }}" class="border border-gray-300 rounded px-3 py-1 focus:ring-blue-500">
            <input type="text" name="search_substansi" placeholder="Cari Substansi" value="{{ request('search_substansi') }}" class="border border-gray-300 rounded px-3 py-1 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded">Cari</button>
            <a href="{{ route('pegawais.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:ring-blue-500">
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

    {{-- ============================================= --}}
    {{-- PERUBAHAN: Menambahkan kelas 'auto-dismiss' --}}
    {{-- ============================================= --}}
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
                                    <a href="{{ route('pegawais.edit', $pegawai->id) }}" class="w-20 bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-sm text-center inline-block mb-1">Edit</a>
                                    <button type="button"
                                            data-action="{{ route('pegawais.destroy', $pegawai->id) }}"
                                            class="open-delete w-20 bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-sm text-center">
                                        Hapus
                                    </button>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 flex justify-center">
            {{ $pegawais->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-600">Tidak ada data pegawai yang tersedia.</p>
    @endif
</div>

{{-- Modal Konfirmasi Delete --}}
<div id="confirm-delete" class="hidden fixed z-50 top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus data pegawai ini?</p>
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
{{-- ============================================= --}}
{{-- PENAMBAHAN: Script untuk Modal & Notifikasi  --}}
{{-- ============================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
