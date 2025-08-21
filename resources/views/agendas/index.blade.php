@extends('layouts.app')

@section('title', 'Data Agenda')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Daftar Agenda</h2>
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'operator']))
        <a href="{{ route('agendas.create') }}" class="bg-blue-500 text-white font-bold px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-block">Tambah Agenda</a>
    @endif

    {{-- Filter dan Pencarian --}}
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
                <input type="text" name="search_kegiatan" placeholder="Cari Kegiatan" value="{{ request('search_kegiatan') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" name="search_asal_surat" placeholder="Cari Asal Surat" value="{{ request('search_asal_surat') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                {{-- ============================================= --}}
                {{-- PERUBAHAN: Input Tanggal menggunakan Flatpickr --}}
                {{-- ============================================= --}}
                <input type="text" id="search_tanggal_mulai" name="search_tanggal_mulai" value="{{ request('search_tanggal_mulai') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari Tanggal Awal">
                <input type="text" id="search_tanggal_akhir" name="search_tanggal_akhir" value="{{ request('search_tanggal_akhir') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cari Tanggal Akhir">
                
                <input type="text" name="search_tempat" placeholder="Cari Tempat" value="{{ request('search_tempat') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @if(auth()->user()->role === 'admin')
                    <input type="text" name="search_substansi" placeholder="Cari Substansi" value="{{ request('search_substansi') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @endif
                <input type="text" name="search_pegawai" placeholder="Cari Pegawai" value="{{ request('search_pegawai') }}" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-4 py-1 rounded text-sm">Cari</button>
                <a href="{{ route('agendas.index') }}" class="text-sm text-gray-600 hover:underline">Reset</a>
            </div>

            <div>
                <select name="sort_by" onchange="this.form.submit()" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Urutkan...</option>
                    <option value="tanggal_terdekat" {{ request('sort_by') == 'tanggal_terdekat' ? 'selected' : '' }}>Tanggal Terdekat</option>
                    <option value="tanggal_terjauh" {{ request('sort_by') == 'tanggal_terjauh' ? 'selected' : '' }}>Tanggal Terjauh</option>
                    <option value="asal_surat" {{ request('sort_by') == 'asal_surat' ? 'selected' : '' }}>Asal Surat</option>
                    <option value="tempat" {{ request('sort_by') == 'tempat' ? 'selected' : '' }}>Tempat</option>
                    @if(auth()->user()->role === 'admin')
                        <option value="substansi" {{ request('sort_by') == 'substansi' ? 'selected' : '' }}>Substansi</option>
                    @endif
                    <option value="terbaru" {{ request('sort_by') == 'terbaru' ? 'selected' : '' }}>Inputan Terbaru</option>
                    <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Inputan Terlama</option>
                </select>
            </div>
        </div>
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

    {{-- Tabel --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full bg-white border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-600 uppercase">No</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Kegiatan</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Asal Surat</th>
                    <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-600 uppercase">Rentang Tanggal</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Tempat</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Substansi</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Nama Pegawai</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600 uppercase">Keterangan</th>
                    <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-600 uppercase">Surat</th>
                    <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-600 uppercase">Surat Tugas</th>
                    <th class="py-2 px-4 border-b text-center text-sm font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($agendas as $agenda)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b text-center text-sm">{{ ($agendas->currentPage() - 1) * $agendas->perPage() + $loop->iteration }}</td>
                    <td class="py-2 px-4 border-b text-sm">{{ $agenda->kegiatan }}</td>
                    <td class="py-2 px-4 border-b text-sm">{{ $agenda->asal_surat }}</td>
                    <td class="py-2 px-4 border-b text-center text-sm">
                        @if($agenda->tanggal_mulai->eq($agenda->tanggal_selesai))
                            {{ $agenda->tanggal_mulai->format('d M Y') }}
                        @else
                            {{ $agenda->tanggal_mulai->format('d M Y') }} - {{ $agenda->tanggal_selesai->format('d M Y') }}
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b text-sm">{{ $agenda->tempat }}</td>
                    <td class="py-2 px-4 border-b text-sm">{{ $agenda->substansi->nama ?? '-' }}</td>
                    <td class="py-2 px-4 border-b text-sm">
                        @foreach ($agenda->pegawais as $pegawai)
                            <span class="inline-block bg-gray-200 text-gray-700 rounded-full px-2 py-1 text-xs mr-1 mb-1">{{ $pegawai->nama }}</span>
                        @endforeach
                    </td>
                    <td class="py-2 px-4 border-b text-sm">{{ \Illuminate\Support\Str::limit($agenda->keterangan_agenda, 50) }}</td>
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
                    <td class="py-2 px-4 border-b text-center text-sm">
                        @if($agenda->surat_tugas && \Illuminate\Support\Facades\Storage::disk('public')->exists($agenda->surat_tugas))
                            <a href="{{ asset('storage/' . $agenda->surat_tugas) }}" target="_blank" class="text-green-600 hover:underline text-xs whitespace-nowrap">Lihat ST</a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b">
                        <div class="flex flex-col items-center space-y-1">
                            <a href="{{ route('agendas.show', $agenda->id) }}" class="w-20 bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded text-xs text-center">Detail</a>
                            @if(in_array(auth()->user()->role, ['admin', 'operator']))
                                <a href="{{ route('agendas.edit', $agenda->id) }}" class="w-20 bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded text-xs text-center">Edit</a>
                                <form action="{{ route('agendas.destroy', $agenda->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus agenda ini?')">
                                    @csrf
                                    @method('DELETE')
                                     <button type="button"
                                            data-action="{{ route('agendas.destroy', $agenda->id) }}"
                                            class="w-20 bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded text-sm text-center open-delete">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-gray-500">Tidak ada agenda ditemukan.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $agendas->appends(request()->except('page'))->links() }}
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="confirm-delete" class="hidden fixed z-50 top-20 left-1/2 transform -translate-x-1/2 bg-white border border-gray-300 shadow-lg rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Hapus</h2>
        <p class="text-sm text-gray-600 mb-4">Apakah Anda yakin ingin menghapus data agenda ini?</p>
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
        // --- Bagian 1: Inisialisasi Flatpickr ---
        flatpickr("#search_tanggal_mulai", { dateFormat: "d-m-Y" });
        flatpickr("#search_tanggal_akhir", { dateFormat: "d-m-Y" });

        // --- Bagian 2: Logika untuk Modal Hapus ---
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

        // --- Bagian 3: Logika untuk Notifikasi Auto-Dismiss ---
        const alerts = document.querySelectorAll('.auto-dismiss');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                // Mulai transisi fade out
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                // Hapus elemen setelah transisi selesai
                setTimeout(function() {
                    alert.remove();
                }, 500); // Cocokkan dengan durasi transisi

            }, 5000); // 5 detik
        });
    });
</script>
@endpush
