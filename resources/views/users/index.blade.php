@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<div class="container mx-auto p-4">

    <h1 class="text-2xl font-semibold mb-4">Daftar Pengguna</h1>

    <!-- Tombol Tambah Pengguna -->
    <a href="{{ route('users.create') }}"
       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Tambah Pengguna
    </a>

    <!-- Form Filter & Pencarian -->
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-4">
        <div>
            <label for="perPage" class="mr-2 font-medium">Tampilkan:</label>
            <select name="perPage" id="perPage" onchange="this.form.submit()" class="border-gray-300 rounded px-2 py-1">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <input type="text" name="search_nama" placeholder="Cari Nama"
               value="{{ request('search_nama') }}"
               class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500" />

        <input type="text" name="search_username" placeholder="Cari Username"
               value="{{ request('search_username') }}"
               class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500" />

        <input type="text" name="search_role" placeholder="Cari Role"
               value="{{ request('search_role') }}"
               class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500" />

        <input type="text" name="search_substansi" placeholder="Cari Substansi"
               value="{{ request('search_substansi') }}"
               class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500" />

        <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded">
            Cari
        </button>

        <a href="{{ route('users.index') }}" class="text-gray-600 hover:underline">Reset</a>

        <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1">
            <option value="">Urutkan Berdasarkan</option>
            <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Inputan Terbaru</option>
            <option value="substansi" {{ request('sort_by') == 'substansi' ? 'selected' : '' }}>Substansi</option>
            <option value="role" {{ request('sort_by') == 'role' ? 'selected' : '' }}>Role</option>
        </select>
    </form>

    {{-- Notifikasi --}}
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

    <!-- Tabel Daftar Pengguna -->
    @if ($users->count() > 0)
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="py-2 px-4 border-b">No</th>
                        <th class="py-2 px-4 border-b">Nama</th>
                        <th class="py-2 px-4 border-b">Username</th>
                        <th class="py-2 px-4 border-b">Role</th>
                        <th class="py-2 px-4 border-b">Substansi</th>
                        <th class="py-2 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr class="hover:bg-gray-50 text-center">
                            <td class="py-2 px-4 border-b">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-2 px-4 border-b">{{ $user->nama }}</td>
                            <td class="py-2 px-4 border-b">{{ $user->username }}</td>
                            <td class="py-2 px-4 border-b capitalize">{{ $user->role }}</td>
                            <td class="py-2 px-4 border-b">{{ $user->substansi->nama ?? '-' }}</td>
                            <td class="py-2 px-4 border-b">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">Edit</a>
                                    
                                    {{-- ============================================= --}}
                                    {{-- PERUBAHAN: Tombol Hapus untuk membuka modal --}}
                                    {{-- ============================================= --}}
                                    <button type="button"
                                            data-action="{{ route('users.destroy', $user) }}"
                                            class="open-delete bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
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
            {{ $users->appends(request()->except('page'))->links() }}
        </div>
    @else
        <p class="text-gray-600">Tidak ada data pengguna yang tersedia.</p>
    @endif

</div>

{{-- ============================================= --}}
{{-- PENAMBAHAN: Modal Konfirmasi Hapus          --}}
{{-- ============================================= --}}
<div id="confirm-delete" class="hidden fixed z-50 top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus pengguna ini?</p>
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
