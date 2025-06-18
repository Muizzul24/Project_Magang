@extends('layouts.app')

@section('title', 'Tambah Agenda')

@section('content')
<div class="container mx-auto p-6 max-w-4xl">
    <h2 class="text-2xl font-semibold mb-6">Tambah Agenda</h2>

    {{-- Tampilkan error validasi jika ada --}}
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
            @error('kegiatan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Asal Surat --}}
        <div class="mb-4">
            <label for="asal_surat" class="block text-sm font-bold text-gray-700 mb-2">Asal Surat:</label>
            <input type="text" name="asal_surat" id="asal_surat" value="{{ old('asal_surat') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @error('asal_surat')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tanggal --}}
        <div class="mb-4">
            <label for="tanggal" class="block text-sm font-bold text-gray-700 mb-2">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @error('tanggal')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tempat --}}
        <div class="mb-4">
            <label for="tempat" class="block text-sm font-bold text-gray-700 mb-2">Tempat:</label>
            <input type="text" name="tempat" id="tempat" value="{{ old('tempat') }}" 
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            @error('tempat')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
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

        {{-- Pegawai (multiple select dengan Select2) --}}
        <div class="mb-4">
            <label for="pegawai_ids" class="block text-sm font-medium text-gray-700 mb-2">Pegawai:</label>
            <select id="pegawai_ids" name="pegawai_ids[]" multiple class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                {{-- Opsi akan diisi via AJAX --}}
            </select>
            @error('pegawai_ids')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Keterangan Agenda --}}
        <div class="mb-4">
            <label for="keterangan_agenda" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Agenda:</label>
            <textarea name="keterangan_agenda" id="keterangan_agenda" rows="4" 
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('keterangan_agenda') }}</textarea>
            @error('keterangan_agenda')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
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

            <p class="text-xs text-gray-500 mt-1">Format: pdf, doc, docx, xls, xlsx. Maks 2MB per file.</p>

            @error('surat')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($errors->has('surat.*'))
                @foreach ($errors->get('surat.*') as $messages)
                    @foreach ($messages as $msg)
                        <p class="mt-2 text-sm text-red-600">{{ $msg }}</p>
                    @endforeach
                @endforeach
            @endif
        </div>

        {{-- Pilih Surat Tugas (dengan pencarian) --}}
        <div class="mb-4">
            <label for="surat_tugas_id" class="block font-semibold mb-1">Pilih Surat Tugas:</label>
            <select name="surat_tugas_id" id="surat_tugas_id" class="select2 w-full border rounded px-3 py-2" required>
                <option value="">Pilih Surat</option>
                @foreach ($suratTugas as $surat)
                    <option value="{{ $surat->id }}">
                        {{ $surat->tujuan }} - {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d-m-Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" 
            class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition duration-200">
            Simpan Agenda
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Inisialisasi Select2 untuk pegawai
        $('#pegawai_ids').select2({
            placeholder: "Pilih Pegawai",
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi Select2 untuk surat tugas
        $('#surat_tugas_id').select2({
            placeholder: "Pilih Surat Tugas",
            allowClear: true,
            width: '100%'
        });

        // Fungsi untuk load pegawai berdasarkan substansi
        function loadPegawai(substansiId, selectedPegawaiIds = []) {
            if (substansiId) {
                $.ajax({
                    url: '/agendas/getPegawaiBySubstansi/' + substansiId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#pegawai_ids').empty();
                        $.each(data, function (key, pegawai) {
                            var selected = selectedPegawaiIds.includes(pegawai.id);
                            var option = new Option(
                                pegawai.nama + ' - ' + pegawai.nip + ' - ' + pegawai.jabatan,
                                pegawai.id,
                                selected,
                                selected
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

        // Event saat substansi berubah
        $('#substansi_id').on('change', function () {
            var substansiId = $(this).val();
            loadPegawai(substansiId);
        });

        // Jika halaman di-load dan ada substansi yang sudah dipilih (misal form validasi gagal)
        var oldSubstansiId = '{{ old('substansi_id') }}';
        var oldPegawaiIds = @json(old('pegawai_ids', []));
        if (oldSubstansiId) {
            loadPegawai(oldSubstansiId, oldPegawaiIds);
        }

        // ==== Tambah Input Surat (Dinamis) ====
        $(document).ready(function () {
            // Tambah input file
            $('#addFileBtn').click(function () {
                const newInput = `
                    <div class="flex items-center gap-2 mb-2">
                        <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                        <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                    </div>
                `;
                $('#fileInputs').append(newInput);
            });

            // Hapus input file
            $(document).on('click', '.removeFileInput', function () {
                $(this).closest('.flex').remove();
            });
        });
    });
</script>
@endpush