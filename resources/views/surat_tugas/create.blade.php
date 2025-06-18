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
            <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat') }}" 
                class="w-full border border-gray-300 rounded px-3 py-2" required>
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
            <label for="substansi_penandatangan_id" class="block font-medium mb-1">Substansi Penandatangan</label>
            <select name="substansi_penandatangan_id" id="substansi_penandatangan_id" class="w-full border px-3 py-2 rounded" required>
                <option value="" disabled selected>Pilih Substansi</option>
                @foreach ($substansis as $substansi)
                    <option value="{{ $substansi->id }}" {{ old('substansi_penandatangan_id') == $substansi->id ? 'selected' : '' }}>
                        {{ $substansi->nama }}
                    </option>
                @endforeach
            </select>
            @error('substansi_penandatangan_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pegawai Penandatangan (tergantung substansi) --}}
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
<script>
    $(document).ready(function () {
        const dasarSuratData = @json($dasarSurats->keyBy('id'));
        const oldSubstansiId = '{{ old('substansi_id') }}';
        const oldPegawaiIds = @json(old('pegawai_ids', []));
        const oldPenandatanganId = '{{ old('penandatangan_id') }}';
        const oldSubstansiPenandatanganId = '{{ old('substansi_penandatangan_id') }}';

        // Inisialisasi semua select2
        $('#pegawai_ids, #penandatangan_id, #dasar_surat_id, #paraf_surat_id, #substansi_penandatangan_id').select2({
            width: '100%',
            allowClear: true
        });

        // Load pegawai berdasarkan substansi (untuk multiple pegawai_ids)
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

        // Load pegawai penandatangan berdasarkan substansi (bukan substansi)
        function loadPenandatanganBySubstansi(substansiId, selectedId = null) {
            if (substansiId) {
                $.ajax({
                    url: '/getPenandatanganBySubstansi/' + substansiId, // ✅ ini route yang benar
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#penandatangan_id').empty().append('<option disabled selected>Pilih Pegawai</option>');
                        $.each(data, function (_, pegawai) {
                            const selected = pegawai.id == selectedId ? 'selected' : '';
                            $('#penandatangan_id').append(
                                `<option value="${pegawai.id}" ${selected}>${pegawai.nama} - ${pegawai.nip} - ${pegawai.jabatan}</option>`
                            );
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

        // Load preview isi dasar surat
        function updatePreviewDasarSurat() {
            const selectedIds = $('#dasar_surat_id').val() || [];
            const previewContainer = $('#preview-dasar-surat');
            previewContainer.empty();

            if (selectedIds.length === 0) {
                previewContainer.html('<p class="text-gray-500 italic">Pilih dasar surat untuk melihat isi...</p>');
                return;
            }

            selectedIds.forEach(id => {
                const dasar = dasarSuratData[id];
                if (dasar) {
                    const html = `
                        <div class="mb-3 p-3 border border-gray-300 rounded bg-white shadow-sm">
                            <h3 class="font-semibold text-blue-700 mb-1">${dasar.nomor} - ${dasar.keterangan}</h3>
                            <p class="text-gray-700 whitespace-pre-line">${dasar.isi || 'Tidak ada isi dasar surat.'}</p>
                        </div>`;
                    previewContainer.append(html);
                }
            });
        }

        // Event ketika substansi dipilih → load pegawai tugas
        $('#substansi_id').on('change', function () {
            const substansiId = $(this).val();
            loadPegawai(substansiId);
        });

        // Event ketika substansi penandatangan dipilih → load pegawai penandatangan
        $('#substansi_penandatangan_id').on('change', function () {
            const substansiId = $(this).val();
            loadPenandatanganBySubstansi(substansiId);
        });

        // Event ketika dasar surat berubah → update preview
        $('#dasar_surat_id').on('change', updatePreviewDasarSurat);

        // Load ulang jika ada old() (form validasi gagal)
        if (oldSubstansiId) {
            loadPegawai(oldSubstansiId, oldPegawaiIds);
        }
        if (oldSubstansiPenandatanganId) {
            loadPenandatanganBySubstansi(oldSubstansiPenandatanganId, oldPenandatanganId);
        }

        updatePreviewDasarSurat();
    });
</script>
@endpush
