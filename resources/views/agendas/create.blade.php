@extends('layouts.app')

@section('title', 'Tambah Agenda')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <h2 class="text-2xl font-semibold mb-6">Tambah Agenda</h2>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('agendas.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        {{-- Kegiatan --}}
        <div class="mb-4">
            <label for="kegiatan" class="block text-sm font-bold text-gray-700 mb-2">Kegiatan:</label>
            <input type="text" name="kegiatan" id="kegiatan" value="{{ old('kegiatan') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        {{-- Asal Surat --}}
        <div class="mb-4">
            <label for="asal_surat" class="block text-sm font-bold text-gray-700 mb-2">Asal Surat:</label>
            <input type="text" name="asal_surat" id="asal_surat" value="{{ old('asal_surat') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div class="flex flex-wrap -mx-3 mb-4">
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label for="tanggal_mulai" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai:</label>
                <input type="text" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="DD-MM-YYYY">
            </div>
            <div class="w-full md:w-1/2 px-3">
                <label for="tanggal_selesai" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai:</label>
                <input type="text" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required placeholder="DD-MM-YYYY">
            </div>
        </div>

        {{-- Tempat --}}
        <div class="mb-4">
            <label for="tempat" class="block text-sm font-bold text-gray-700 mb-2">Tempat:</label>
            <input type="text" name="tempat" id="tempat" value="{{ old('tempat') }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        {{-- Substansi --}}
        <div class="mb-4">
            <label for="substansi_id" class="block text-gray-700 font-bold mb-2">Substansi:</label>
            <select name="substansi_id" id="substansi_id" class="w-full border rounded px-3 py-2" required>
                <option value="" disabled {{ old('substansi_id') ? '' : 'selected' }}>Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_id') == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pegawai --}}
        <div class="mb-4">
            <label for="pegawai_ids" class="block text-sm font-medium text-gray-700 mb-2">Pegawai:</label>
            <select id="pegawai_ids" name="pegawai_ids[]" multiple class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                {{-- Diisi via JS --}}
            </select>
        </div>

        {{-- Keterangan Agenda --}}
        <div class="mb-4">
            <label for="keterangan_agenda" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Agenda:</label>
            <textarea name="keterangan_agenda" id="keterangan_agenda" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('keterangan_agenda') }}</textarea>
        </div>

        {{-- Upload Surat --}}
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Surat (opsional):</label>
            <div id="fileInputs">
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>
            </div>
            <button type="button" id="addFileBtn"
                    class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded mt-1">
                + Tambah File
            </button>
        </div>

        {{-- Pilih Surat Tugas --}}
        <div class="mb-4">
            <label for="surat_tugas_id" class="block font-semibold mb-1">Pilih Surat Tugas:</label>
            <select name="surat_tugas_id" id="surat_tugas_id" class="select2 w-full border rounded px-3 py-2" required>
                <option value="">Pilih Surat</option>
                @foreach ($suratTugas as $surat)
                    <option value="{{ $surat->id }}" {{ old('surat_tugas_id') == $surat->id ? 'selected' : '' }}>
                        {{ $surat->tujuan }} - {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" 
                class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
            Simpan Agenda
        </button>
        <a href="{{ route('agendas.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Batal</a>
    </form>
</div>
@endsection

@push('scripts')
<!-- JQuery (diperlukan oleh Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Flatpickr -->
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

        flatpickr("#tanggal_mulai", {
            dateFormat: "d-m-Y",
            defaultDate: "{{ old('tanggal_mulai') }}"
        });

        flatpickr("#tanggal_selesai", {
            dateFormat: "d-m-Y",
            defaultDate: "{{ old('tanggal_selesai') }}"
        });

        // Ambil pegawai berdasarkan substansi
        function loadPegawai(substansiId, selectedPegawaiIds = []) {
            if (substansiId) {
                $.ajax({
                    url: '/agendas/getPegawaiBySubstansi/' + substansiId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#pegawai_ids').empty();
                        $.each(data, function (key, pegawai) {
                            // Cek jika ID pegawai ada di array oldPegawaiIds
                            let isSelected = selectedPegawaiIds.some(id => id == pegawai.id);
                            let option = new Option(
                                pegawai.nama + ' - ' + pegawai.nip + ' - ' + pegawai.jabatan,
                                pegawai.id,
                                isSelected,
                                isSelected
                            );
                            $('#pegawai_ids').append(option);
                        });
                        $('#pegawai_ids').trigger('change');
                    },
                    error: function () {
                        alert('Gagal mengambil data pegawai.');
                    }
                });
            } else {
                $('#pegawai_ids').empty().trigger('change');
            }
        }

        $('#substansi_id').on('change', function () {
            var substansiId = $(this).val();
            loadPegawai(substansiId);
        });

        // Jika ada old input saat validasi gagal
        var oldSubstansiId = '{{ old('substansi_id') }}';
        var oldPegawaiIds = @json(old('pegawai_ids', []));
        if (oldSubstansiId) {
            loadPegawai(oldSubstansiId, oldPegawaiIds);
        }

        // Upload file dinamis
        $('#addFileBtn').click(function () {
            const newInput = `
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>`;
            $('#fileInputs').append(newInput);
        });

        $(document).on('click', '.removeFileInput', function () {
            $(this).closest('.flex').remove();
        });
    });
</script>
@endpush
