@extends('layouts.app')

@section('title', 'Dasar Surat')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Daftar Dasar Surat</h1>

    <!-- Tombol Tambah Dasar Surat -->
    <a href="{{ route('dasarSurat.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Tambah Dasar Surat
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
                placeholder="Cari Dasar Surat" 
                value="{{ request('search') }}" 
                class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded"
            >
                Cari
            </button>
            <a href="{{ route('dasarSurat.index') }}" class="ml-2 text-gray-600 hover:underline">Reset</a>

            <!-- Sorting -->
            <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Urutkan Berdasarkan</option>
                <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Inputan Terbaru</option>
                <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Inputan Terlama</option>
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

    <!-- Tabel Dasar Surat -->
    @if($dasarSurats->count())
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b text-left">Dasar Surat</th>
                        <th class="py-2 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($dasarSurats as $index => $dasarSurat)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $dasarSurats->firstItem() + $index }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $dasarSurat->dasar_surat }}</td>
                            <td class="py-2 px-4 border-b">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('dasarSurat.edit', $dasarSurat->id) }}"
                                       class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded text-sm font-bold">Edit</a>
                                    <button type="button"
                                            data-action="{{ route('dasarSurat.destroy', $dasarSurat->id) }}"
                                            class="open-delete bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded text-sm font-bold">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            {{ $dasarSurats->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-600 mt-4">Data dasar surat belum tersedia.</p>
    @endif
</div>

{{-- Modal Konfirmasi Delete --}}
<div id="confirm-delete" class="hidden fixed z-50 top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus data dasar surat ini?</p>
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
