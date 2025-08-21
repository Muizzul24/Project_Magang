@extends('layouts.app')

@section('title', 'Arsip Agenda')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Arsip Agenda</h1>
        {{-- ============================================= --}}
        {{-- PERUBAHAN: Tombol dibatasi untuk admin & operator --}}
        {{-- ============================================= --}}
        @if(in_array(auth()->user()->role, ['admin', 'operator']))
            <form action="{{ route('agendas.arsip.store') }}" method="POST" onsubmit="return confirm('Pindahkan semua agenda yang tanggalnya sudah lewat ke arsip?')">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm inline-block">
                    <i class="fas fa-archive mr-2"></i>Arsipkan Agenda Terlewat
                </button>
            </form>
        @endif
    </div>

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

    <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg border">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <label for="perPage" class="mr-2 font-medium text-sm">Tampilkan:</label>
                <select name="perPage" id="perPage" onchange="this.form.submit()" class="border-gray-300 rounded px-2 py-1 text-sm">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow flex flex-wrap gap-2 items-center">
                <input type="text" name="search_kegiatan" placeholder="Cari Kegiatan" value="{{ request('search_kegiatan') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-blue-500">
                <input type="text" id="search_tanggal_mulai" name="search_tanggal_mulai" value="{{ request('search_tanggal_mulai') }}" class="border border-gray-300 rounded px-3 py-1 text-sm" placeholder="Dari Tanggal">
                <input type="text" id="search_tanggal_akhir" name="search_tanggal_akhir" value="{{ request('search_tanggal_akhir') }}" class="border border-gray-300 rounded px-3 py-1 text-sm" placeholder="Sampai Tanggal">
                <input type="text" name="search_pegawai" placeholder="Cari Pegawai" value="{{ request('search_pegawai') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded text-sm">Cari</button>
                <a href="{{ route('agendas.arsip') }}" class="ml-2 text-gray-600 hover:underline text-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
        <table class="min-w-full bg-white border border-gray-300">
            <thead class="bg-gray-200">
                <tr class="text-sm">
                    <th class="py-2 px-4 border-b text-center">No</th>
                    <th class="py-2 px-4 border-b text-left">Kegiatan</th>
                    <th class="py-2 px-4 border-b text-left">Asal Surat</th>
                    <th class="py-2 px-4 border-b text-center">Rentang Tanggal</th>
                    <th class="py-2 px-4 border-b text-left">Tempat</th>
                    <th class="py-2 px-4 border-b text-center">Substansi</th>
                    <th class="py-2 px-4 border-b text-left">Pegawai</th>
                    <th class="py-2 px-4 border-b text-center">Surat</th>
                    <th class="py-2 px-4 border-b text-center">Surat Tugas</th>
                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agendaArsip as $agenda)
                    <tr class="hover:bg-gray-50 text-center text-sm">
                        <td class="py-2 px-4 border-b text-center">{{ ($agendaArsip->currentPage() - 1) * $agendaArsip->perPage() + $loop->iteration }}</td>
                        <td class="py-2 px-4 border-b text-left">{{ $agenda->kegiatan }}</td>
                        <td class="py-2 px-4 border-b text-left">{{ $agenda->asal_surat }}</td>
                        <td class="py-2 px-4 border-b">
                             @if($agenda->tanggal_mulai->eq($agenda->tanggal_selesai))
                                {{ $agenda->tanggal_mulai->isoFormat('D MMM Y') }}
                            @else
                                {{ $agenda->tanggal_mulai->isoFormat('D MMM') }} - {{ $agenda->tanggal_selesai->isoFormat('D MMM Y') }}
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b text-left">{{ $agenda->tempat }}</td>
                        <td class="py-2 px-4 border-b">{{ $agenda->substansi->nama ?? '-' }}</td>
                        <td class="py-2 px-4 border-b text-left">
                            @foreach ($agenda->pegawais as $pegawai)
                                <span class="inline-block bg-gray-200 text-gray-700 rounded-full px-2 py-1 text-xs mr-1 mb-1">{{ $pegawai->nama }}</span>
                            @endforeach
                        </td>
                        <td class="py-2 px-4 border-b text-center text-sm">
                            @if($agenda->surat)
                                @php $files = explode(',', $agenda->surat); @endphp
                                <div class="space-y-1">
                                    @foreach($files as $index => $file)
                                        @if(!empty($file))
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-500 hover:underline text-xs whitespace-nowrap">Lihat Surat {{ $index + 1 }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            @if($agenda->surat_tugas && \Illuminate\Support\Facades\Storage::disk('public')->exists($agenda->surat_tugas))
                                <a href="{{ asset('storage/' . $agenda->surat_tugas) }}" target="_blank" class="text-green-600 hover:underline text-xs whitespace-nowrap">Lihat ST</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b">
                            <div class="flex flex-col items-center space-y-1">
                                <a href="{{ route('agendas.show', $agenda->id) }}" class="w-24 bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-xs text-center">Detail</a>
                                
                                @if(in_array(auth()->user()->role, ['admin', 'operator']))
                                    <button type="button"
                                            data-action="{{ route('agendas.destroy', $agenda->id) }}"
                                            class="open-delete w-24 bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-xs text-center">
                                        Hapus Permanen
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-gray-500">Tidak ada agenda yang diarsipkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex justify-center">
        {{ $agendaArsip->links('pagination::tailwind') }}
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="confirm-delete" class="hidden fixed z-50 top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus agenda ini secara permanen?</p>
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
        flatpickr("#search_tanggal_mulai", { dateFormat: "d-m-Y" });
        flatpickr("#search_tanggal_akhir", { dateFormat: "d-m-Y" });

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
