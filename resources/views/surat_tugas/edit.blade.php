@extends('layouts.app')

@section('title', 'Edit Surat Tugas')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow-md mt-8">
    <h1 class="text-2xl font-semibold mb-6">Edit Surat Tugas</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat_tugas.update', $suratTugas->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Nomor Surat --}}
        <div>
            <label for="nomor_surat" class="block font-medium mb-1">Nomor Surat</label>
            <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $suratTugas->nomor_surat) }}"
                class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        {{-- Dasar Surat --}}
        <div>
            <label for="dasar_surat_id" class="block font-medium mb-1">Dasar Surat</label>
            <select name="dasar_surat_id[]" id="dasar_surat_id" class="select2 w-full border px-3 py-2 rounded" multiple required>
                @foreach ($dasarSurats as $dasar)
                    <option value="{{ $dasar->id }}" {{ collect(old('dasar_surat_id', $suratTugas->dasarSurat->pluck('id')))->contains($dasar->id) ? 'selected' : '' }}>
                        {{ $dasar->dasar_surat }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Substansi --}}
        <div>
            <label for="substansi_id" class="block font-medium mb-1">Substansi</label>
            <select name="substansi_id" id="substansi_id" class="w-full border px-3 py-2 rounded" required>
                <option value="" disabled selected>Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_id', $suratTugas->substansi_id) == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pegawai --}}
        <div>
            <label for="pegawai_ids" class="block font-medium mb-1">Pegawai</label>
            <select name="pegawai_ids[]" id="pegawai_ids" class="select2 w-full border px-3 py-2 rounded" multiple required>
                {{-- Akan di-load via AJAX --}}
            </select>
        </div>

        {{-- Tujuan --}}
        <div>
            <label for="tujuan" class="block font-medium mb-1">Tujuan</label>
            <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan', $suratTugas->tujuan) }}"
                class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        {{-- Tanggal Surat --}}
        <div>
            <label for="tanggal_surat" class="block font-medium mb-1">Tanggal Surat</label>
            <input type="date" name="tanggal_surat" id="tanggal_surat"
                value="{{ old('tanggal_surat', $suratTugas->tanggal_surat->format('Y-m-d')) }}"
                class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>

        {{-- Paraf Surat --}}
        <div>
            <label for="paraf_surat_id" class="block font-medium mb-1">Paraf Surat</label>
            <select name="paraf_surat_id[]" id="paraf_surat_id" class="select2 w-full border px-3 py-2 rounded" multiple required>
                @foreach ($parafSurats as $paraf)
                    <option value="{{ $paraf->id }}" {{ collect(old('paraf_surat_id', $suratTugas->parafSurat->pluck('id')))->contains($paraf->id) ? 'selected' : '' }}>
                        {{ $paraf->paraf_surat }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Substansi Penandatangan --}}
        <div>
            <label for="substansi_penandatangan_id" class="block font-medium mb-1">Substansi Penandatangan</label>
            <select name="substansi_penandatangan_id" id="substansi_penandatangan_id" class="select2 w-full border px-3 py-2 rounded" required>
                <option value="" disabled selected>Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_penandatangan_id', $suratTugas->substansi_penandatangan_id) == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pegawai Penandatangan --}}
        <div>
            <label for="penandatangan_id" class="block font-medium mb-1">Pegawai Penandatangan</label>
            <select name="penandatangan_id" id="penandatangan_id" class="select2 w-full border px-3 py-2 rounded" required>
                {{-- Akan diisi via AJAX --}}
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center">
            <button type="submit" class="bg-blue-600 text-white font-semibold px-5 py-2 rounded hover:bg-blue-700 transition duration-200">
                Update
            </button>
            <a href="{{ route('surat_tugas.index') }}" class="ml-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-5 py-2 rounded transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const dasarSuratData = @json($dasarSurats->keyBy('id'));
    const oldSubstansiId = '{{ old('substansi_id', $suratTugas->substansi_id) }}';
    const oldPegawaiIds = @json(old('pegawai_ids', $suratTugas->pegawais->pluck('id')));
    const oldPenandatanganId = '{{ old('penandatangan_id', $suratTugas->penandatangan_id) }}';
    const oldSubstansiPenandatanganId = '{{ old('substansi_penandatangan_id', $suratTugas->substansi_penandatangan_id) }}';

    $('#pegawai_ids, #penandatangan_id, #dasar_surat_id, #paraf_surat_id, #substansi_penandatangan_id').select2({ width: '100%', allowClear: true });

    function loadPegawai(substansiId, selectedPegawaiIds = []) {
        if (substansiId) {
            $.ajax({
                url: '/surat_tugas/getPegawaiBySubstansi/' + substansiId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#pegawai_ids').empty();
                    $.each(data, function (_, pegawai) {
                        const isSelected = selectedPegawaiIds.includes(pegawai.id);
                        const option = new Option(`${pegawai.nama} - ${pegawai.nip} - ${pegawai.jabatan}`, pegawai.id, isSelected, isSelected);
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
                        $('#penandatangan_id').append(`<option value="${pegawai.id}" ${selected}>${pegawai.nama} - ${pegawai.nip} - ${pegawai.jabatan}</option>`);
                    });
                    $('#penandatangan_id').trigger('change');
                },
                error: function (xhr) {
                    alert('Gagal memuat pegawai penandatangan.\nStatus: ' + xhr.status + '\nResponse: ' + xhr.responseText);
                }
            });
        } else {
            $('#penandatangan_id').empty().trigger('change');
        }
    }

    $('#substansi_id').on('change', function () {
        const substansiId = $(this).val();
        loadPegawai(substansiId);
    });

    $('#substansi_penandatangan_id').on('change', function () {
        const substansiId = $(this).val();
        loadPenandatanganBySubstansi(substansiId);
    });

    // Initial load
    if (oldSubstansiId) loadPegawai(oldSubstansiId, oldPegawaiIds);
    if (oldSubstansiPenandatanganId) loadPenandatanganBySubstansi(oldSubstansiPenandatanganId, oldPenandatanganId);
});
</script>
@endpush
