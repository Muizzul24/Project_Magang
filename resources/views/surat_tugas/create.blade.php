@extends('layouts.app')

@section('title', 'Tambah Surat Tugas')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow-md mt-8">
    <h1 class="text-2xl font-semibold mb-6">Tambah Surat Tugas</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat_tugas.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Nomor Surat --}}
        <div>
            <label for="nomor_surat" class="block font-medium mb-1">Nomor Surat</label>
            <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}" 
                   class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('nomor_surat')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Dasar Surat --}}
        <div class="mb-4">
            <label for="dasar_surat_id" class="form-label font-semibold mb-1">Dasar Surat</label>
            <select name="dasar_surat_id[]" id="dasar_surat_id" class="form-select w-full border rounded px-3 py-2" multiple required>
                @foreach ($dasarSurats as $dasar)
                <option value="{{ $dasar->id }}" {{ (collect(old('dasar_surat_id'))->contains($dasar->id)) ? 'selected' : '' }}>
                    {{ $dasar->dasar_surat}}
                </option>
                @endforeach
            </select>
            @error('dasar_surat_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
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
            @error('substansi_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pegawai (multiple) --}}
        <div class="mb-4">
            <label for="pegawai_ids" class="block text-sm font-medium text-gray-700 mb-2">Pegawai:</label>
            <select id="pegawai_ids" name="pegawai_ids[]" multiple class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                {{-- via AJAX --}}
            </select>
            @error('pegawai_ids')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tujuan --}}
        <div>
            <label for="tujuan" class="block font-medium mb-1">Tujuan</label>
            <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}" 
                   class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('tujuan')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tanggal Surat --}}
        <div>
            <label for="tanggal_surat" class="block font-medium mb-1">Tanggal Surat</label>
            <input type="text" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2" required placeholder="DD-MM-YYYY">
            @error('tanggal_surat')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Paraf Surat --}}
        <div class="mb-4">
            <label for="paraf_surat_id" class="form-label font-semibold mb-1">Paraf Surat</label>
            <select name="paraf_surat_id[]" id="paraf_surat_id" class="form-select w-full border rounded px-3 py-2" multiple required>
                @foreach ($parafSurats as $paraf)
                <option value="{{ $paraf->id }}" {{ (collect(old('paraf_surat_id'))->contains($paraf->id)) ? 'selected' : '' }}>
                    {{ $paraf->paraf_surat}}
                </option>
                @endforeach
            </select>
            @error('paraf_surat_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Substansi Penandatangan --}}
        <div class="mb-4">
            <label for="substansi_penandatangan_id" class="block text-sm font-medium text-gray-700 mb-2">Substansi Penandatangan</label>
            <select id="substansi_penandatangan_id" name="substansi_penandatangan_id" class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Pilih Substansi</option>
                @foreach ($substansiPenandatangan as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_penandatangan_id') == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
            @error('substansi_penandatangan_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pegawai Penandatangan --}}
        <div class="mb-4">
            <label for="penandatangan_id" class="block text-sm font-medium text-gray-700 mb-2">Pegawai Penandatangan</label>
            <select id="penandatangan_id" name="penandatangan_id" class="select2 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="" disabled selected>Pilih Pegawai</option>
            </select>
            @error('penandatangan_id')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="flex items-center">
            <button type="submit" class="bg-blue-600 text-white font-semibold px-5 py-2 rounded hover:bg-blue-700 transition duration-200">
                Simpan
            </button>
            <a href="{{ route('surat_tugas.index') }}" 
               class="ml-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-5 py-2 rounded transition duration-200 inline-block">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- ============================================= --}}
{{-- PERBAIKAN: Menggabungkan semua skrip ke satu blok --}}
{{-- ============================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Flatpickr untuk input tanggal surat
        flatpickr("#tanggal_surat", {
            dateFormat: "d-m-Y", // Format tampilan DD-MM-YYYY
        });

        // Inisialisasi JQuery dan Select2 (jika belum ada)
        if (typeof $ === 'undefined') {
            let script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            document.head.appendChild(script);
            script.onload = initializeSelect2;
        } else {
            initializeSelect2();
        }

        function initializeSelect2() {
            const oldSubstansiId = '{{ old('substansi_id') }}';
            const oldPegawaiIds = @json(old('pegawai_ids', []));
            const oldPenandatanganId = '{{ old('penandatangan_id') }}';
            const oldSubstansiPenandatanganId = '{{ old('substansi_penandatangan_id') }}';

            // Inisialisasi semua select2
            $('#pegawai_ids, #penandatangan_id, #dasar_surat_id, #paraf_surat_id, #substansi_penandatangan_id, #substansi_id').select2({
                width: '100%',
                allowClear: true
            });

            // Load pegawai berdasarkan substansi
            function loadPegawai(substansiId, selectedPegawaiIds = []) {
                if (substansiId) {
                    $.ajax({
                        url: '/surat_tugas/getPegawaiBySubstansi/' + substansiId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#pegawai_ids').empty();
                            $.each(data, function (_, pegawai) {
                                const isSelected = selectedPegawaiIds.includes(pegawai.id.toString());
                                const option = new Option(`${pegawai.nama} - ${pegawai.nip}`, pegawai.id, isSelected, isSelected);
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

            // Load pegawai penandatangan
            function loadPenandatanganBySubstansi(substansiId, selectedId = null) {
                if (substansiId) {
                    $.ajax({
                        url: '/getPenandatanganBySubstansi/' + substansiId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#penandatangan_id').empty().append('<option disabled selected>Pilih Pegawai</option>');
                            $.each(data, function (_, pegawai) {
                                const selected = pegawai.id == selectedId ? 'selected' : '';
                                $('#penandatangan_id').append(
                                    `<option value="${pegawai.id}" ${selected}>${pegawai.nama} - ${pegawai.nip}</option>`
                                );
                            });
                            $('#penandatangan_id').trigger('change');
                        },
                        error: function (xhr) {
                            alert('Gagal memuat pegawai penandatangan.');
                        }
                    });
                } else {
                    $('#penandatangan_id').empty().trigger('change');
                }
            }

            // Event listeners
            $('#substansi_id').on('change', function () {
                loadPegawai($(this).val());
            });

            $('#substansi_penandatangan_id').on('change', function () {
                loadPenandatanganBySubstansi($(this).val());
            });

            // Load ulang jika ada old()
            if (oldSubstansiId) {
                loadPegawai(oldSubstansiId, oldPegawaiIds);
            }
            if (oldSubstansiPenandatanganId) {
                loadPenandatanganBySubstansi(oldSubstansiPenandatanganId, oldPenandatanganId);
            }
        }
    });
</script>
@endpush
