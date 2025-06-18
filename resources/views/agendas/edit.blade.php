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

        {{-- Tanggal --}}
        <div class="mb-4">
            <label for="tanggal" class="block text-sm font-bold text-gray-700 mb-2">Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $agenda->tanggal) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Surat (opsional):</label>

            <div id="fileInputs">
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>
            </div>

            <button type="button" id="addFileBtn" class="text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded mt-1">+ Tambah File</button>

            <p class="text-xs text-gray-500 mt-1">Format: pdf, doc, docx, xls, xlsx. Maks 2MB per file.</p>

            {{-- File yang sudah ada --}}
            @if ($agenda->surat)
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">File Surat yang Sudah Ada:</h4>
                    <div id="existing-files">
                        @php
                            $files = explode(',', $agenda->surat);
                        @endphp
                        @foreach ($files as $index => $file)
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
<script>
    $(document).ready(function () {
        $('#pegawai_ids').select2({ 
            placeholder: "Pilih Pegawai",
            allowClear: true
        });

        // Preload selected pegawai
        let selectedPegawai = @json($agenda->pegawais->map(function($p) {
            return ['id' => $p->id, 'text' => $p->nama];
        }));

        selectedPegawai.forEach(p => {
            var option = new Option(p.text, p.id, true, true);
            $('#pegawai_ids').append(option).trigger('change');
        });

        // Tambah input file baru
        $('#addFileBtn').click(function () {
            $('#fileInputs').append(`
                <div class="flex items-center gap-2 mb-2">
                    <input type="file" name="surat[]" class="border border-gray-300 rounded px-3 py-2 w-full" />
                    <button type="button" class="removeFileInput text-red-500 hover:text-red-700 text-sm font-semibold">✕</button>
                </div>
            `);
        });

        // Hapus input file baru
        $('#fileInputs').on('click', '.removeFileInput', function () {
            $(this).closest('.flex').remove();
        });

        // Hapus file yang sudah ada
        $(document).on('click', '.delete-file-btn', function () {
            const agendaId = $(this).data('agenda-id');
            const fileIndex = $(this).data('file-index');
            const fileName = $(this).data('file-name');
            const $fileElement = $(this).closest('[data-file-index]');
            const $deleteBtn = $(this);

            if (confirm(`Apakah Anda yakin ingin menghapus file "${fileName}"?`)) {
                // Tampilkan loading
                $deleteBtn.text('Menghapus...').prop('disabled', true);
                
                $.ajax({
                    url: `/agendas/${agendaId}/delete-file/${fileIndex}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function (response) {
                        console.log('Success response:', response);
                        
                        if (response.success) {
                            // Hapus elemen dari DOM
                            $fileElement.fadeOut(300, function() {
                                $(this).remove();
                                
                                // Re-index semua file yang tersisa
                                $('#existing-files [data-file-index]').each(function(newIndex) {
                                    $(this).attr('data-file-index', newIndex);
                                    $(this).find('.delete-file-btn').attr('data-file-index', newIndex);
                                });
                                
                                // Cek apakah masih ada file
                                if ($('#existing-files [data-file-index]').length === 0) {
                                    $('#existing-files').parent().hide();
                                }
                            });
                            
                            // Tampilkan notifikasi sukses
                            showNotification('File berhasil dihapus!', 'success');
                        } else {
                            $deleteBtn.text('Hapus').prop('disabled', false);
                            showNotification('Gagal menghapus file: ' + (response.error || 'Unknown error'), 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        $deleteBtn.text('Hapus').prop('disabled', false);
                        
                        let errorMsg = 'Gagal menghapus file. Coba lagi.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.status === 403) {
                            errorMsg = 'Tidak diizinkan menghapus file ini.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'File tidak ditemukan.';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Terjadi kesalahan server.';
                        }
                        
                        showNotification(errorMsg, 'error');
                    }
                });
            }
        });
        
        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = $(`
                <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 notification">
                    ${message}
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
</script>
@endpush
