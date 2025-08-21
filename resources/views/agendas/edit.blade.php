@extends('layouts.app')

@section('title', 'Edit Agenda')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <h2 class="text-2xl font-semibold mb-6">Edit Agenda</h2>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agendas.update', $agenda->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        {{-- Kegiatan --}}
        <div class="mb-4">
            <label for="kegiatan" class="block text-sm font-bold text-gray-700 mb-2">Kegiatan:</label>
            <input type="text" name="kegiatan" id="kegiatan" value="{{ old('kegiatan', $agenda->kegiatan) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        {{-- Asal Surat --}}
        <div class="mb-4">
            <label for="asal_surat" class="block text-sm font-bold text-gray-700 mb-2">Asal Surat:</label>
            <input type="text" name="asal_surat" id="asal_surat" value="{{ old('asal_surat', $agenda->asal_surat) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        {{-- ============================================= --}}
        {{-- PERUBAHAN: Input Rentang Tanggal (Sama seperti Create) --}}
        {{-- ============================================= --}}
        <div class="flex flex-wrap -mx-3 mb-4">
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label for="tanggal_mulai" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai:</label>
                <input type="text" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', $agenda->tanggal_mulai->format('d-m-Y')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="DD-MM-YYYY">
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label for="tanggal_selesai" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai:</label>
                <input type="text" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', $agenda->tanggal_selesai->format('d-m-Y')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="DD-MM-YYYY">
            </div>
        </div>

        {{-- Tempat --}}
        <div class="mb-4">
            <label for="tempat" class="block text-sm font-bold text-gray-700 mb-2">Tempat:</label>
            <input type="text" name="tempat" id="tempat" value="{{ old('tempat', $agenda->tempat) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        {{-- Substansi --}}
        <div class="mb-4">
            <label for="substansi_id" class="block text-sm font-bold text-gray-700 mb-2">Substansi:</label>
            <select name="substansi_id" id="substansi_id" class="w-full border rounded px-3 py-2" required>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_id', $agenda->substansi_id) == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pegawai --}}
        <div class="mb-4">
            <label for="pegawai_ids" class="block text-sm font-medium text-gray-700 mb-2">Pegawai:</label>
            <select id="pegawai_ids" name="pegawai_ids[]" multiple class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                {{-- Loaded by JS --}}
            </select>
        </div>

        {{-- Keterangan Agenda --}}
        <div class="mb-4">
            <label for="keterangan_agenda" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Agenda:</label>
            <textarea name="keterangan_agenda" id="keterangan_agenda" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('keterangan_agenda', $agenda->keterangan_agenda) }}</textarea>
        </div>

        {{-- Upload Surat --}}
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Tambah Surat Baru (opsional):</label>
            <div id="fileInputs">
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>
            </div>
            <button type="button" id="addFileBtn" class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded mt-1">+ Tambah File</button>

            {{-- File yang sudah ada --}}
            @if ($agenda->surat)
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">File Surat yang Sudah Ada:</h4>
                    <div id="existing-files">
                        @php $files = explode(',', $agenda->surat); @endphp
                        @foreach ($files as $index => $file)
                            @if(!empty($file))
                            <div class="flex items-center gap-2 text-sm mt-1 p-2 bg-gray-50 rounded" data-file-index="{{ $index }}">
                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-600 underline flex-1">
                                    {{ basename($file) }}
                                </a>
                                <button type="button" 
                                        class="delete-file-btn text-red-600 hover:text-red-800 text-xs font-semibold px-2 py-1 bg-red-100 hover:bg-red-200 rounded"
                                        data-agenda-id="{{ $agenda->id }}"
                                        data-file-index="{{ $index }}"
                                        data-file-name="{{ basename($file) }}">
                                    Hapus
                                </button>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Surat Tugas --}}
        <div class="mb-4">
            <label for="surat_tugas_id" class="block font-semibold mb-1">Pilih Surat Tugas:</label>
            <select name="surat_tugas_id" id="surat_tugas_id" class="select2 w-full border rounded px-3 py-2" required>
                <option value="">Pilih Surat</option>
                @foreach ($suratTugas as $surat)
                    <option value="{{ $surat->id }}" {{ old('surat_tugas_id', $agenda->surat_tugas_id) == $surat->id ? 'selected' : '' }}>
                        {{ $surat->tujuan }} - {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end space-x-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition duration-200">Update</button>
            <a href="{{ route('agendas.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition duration-200">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- JQuery, Select2, Flatpickr -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('#pegawai_ids, #surat_tugas_id, #substansi_id').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi Flatpickr
        flatpickr("#tanggal_mulai", {
            dateFormat: "d-m-Y",
        });
        flatpickr("#tanggal_selesai", {
            dateFormat: "d-m-Y",
        });

        // Fungsi load pegawai
        function loadPegawai(substansiId, selectedPegawaiIds = []) {
            if (substansiId) {
                $.ajax({
                    url: '/agendas/getPegawaiBySubstansi/' + substansiId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#pegawai_ids').empty();
                        data.forEach(pegawai => {
                            let isSelected = selectedPegawaiIds.includes(pegawai.id.toString());
                            let option = new Option(
                                pegawai.nama + ' - ' + pegawai.nip + ' - ' + pegawai.jabatan,
                                pegawai.id, isSelected, isSelected
                            );
                            $('#pegawai_ids').append(option);
                        });
                        $('#pegawai_ids').trigger('change');
                    },
                    error: function () { alert('Gagal mengambil data pegawai.'); }
                });
            } else {
                $('#pegawai_ids').empty().trigger('change');
            }
        }

        // Event listener untuk perubahan substansi
        $('#substansi_id').on('change', function () {
            loadPegawai($(this).val(), []);
        });

        // Load data awal saat halaman dibuka
        var initialSubstansiId = '{{ old('substansi_id', $agenda->substansi_id) }}';
        var selectedPegawaiIds = @json(old('pegawai_ids', $agenda->pegawais->pluck('id')->map('strval')));
        if(initialSubstansiId) {
            loadPegawai(initialSubstansiId, selectedPegawaiIds);
        }

        // Fungsi tambah/hapus input file
        $('#addFileBtn').click(function () {
            $('#fileInputs').append(`
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>
            `);
        });
        $('#fileInputs').on('click', '.removeFileInput', function () {
            $(this).closest('.flex').remove();
        });

        // Fungsi hapus file lama via AJAX
        $(document).on('click', '.delete-file-btn', function () {
            const button = $(this);
            const agendaId = button.data('agenda-id');
            const fileIndex = button.data('file-index');
            const fileName = button.data('file-name');
            const fileElement = button.closest('[data-file-index]');
            
            if (confirm(`Apakah Anda yakin ingin menghapus file "${fileName}"?`)) {
                $.ajax({
                    url: `/agendas/${agendaId}/delete-file/${fileIndex}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.success) {
                            fileElement.fadeOut(300, function () { $(this).remove(); });
                            alert('File berhasil dihapus.');
                        } else {
                            alert('Gagal menghapus file: ' + response.error);
                        }
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan: ' + (xhr.responseJSON.error || 'Silakan coba lagi.'));
                    }
                });
            }
        });
    });
</script>
@endpush
