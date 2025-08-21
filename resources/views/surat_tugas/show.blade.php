@extends('layouts.app')

@section('title', 'Detail Surat Tugas')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    
    {{-- Header Halaman dengan Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">{{ $suratTugas->tujuan }}</h1>
            <p class="text-sm text-gray-500 mt-1">Detail Surat Tugas - {{ $suratTugas->nomor_surat }}</p>
        </div>
        <div class="flex items-center space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('surat_tugas.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded text-sm transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'operator']))
                <a href="{{ route('surat_tugas.edit', $suratTugas->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded text-sm transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Layout Grid untuk Konten --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Utama (Kiri) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Detail Utama --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Informasi Surat</h3>
                <dl class="space-y-4">
                    <div class="flex items-start">
                        <dt class="w-1/3 text-sm font-medium text-gray-500 flex items-center">
                            <i class="fas fa-hashtag w-5 text-center mr-3 text-blue-500"></i>
                            <span>Nomor Surat</span>
                        </dt>
                        <dd class="w-2/3 text-sm text-gray-900">{{ $suratTugas->nomor_surat }}</dd>
                    </div>
                    <div class="flex items-start">
                        <dt class="w-1/3 text-sm font-medium text-gray-500 flex items-center">
                            <i class="fas fa-calendar-alt w-5 text-center mr-3 text-red-500"></i>
                            <span>Tanggal Surat</span>
                        </dt>
                        <dd class="w-2/3 text-sm text-gray-900">{{ $suratTugas->tanggal_surat->isoFormat('dddd, D MMMM Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Dasar Surat --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Dasar Surat</h3>
                <ul class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                    @forelse($suratTugas->dasarSurat as $dasar)
                        <li>{{ $dasar->dasar_surat }}</li>
                    @empty
                        <p class="text-sm text-gray-500 italic">Tidak ada dasar surat.</p>
                    @endforelse
                </ul>
            </div>

            {{-- Paraf Surat --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Paraf Surat</h3>
                <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
                    @forelse($suratTugas->parafSurat as $paraf)
                        <li>{{ $paraf->paraf_surat }}</li>
                    @empty
                        <p class="text-sm text-gray-500 italic">Tidak ada paraf surat.</p>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Kolom Samping (Kanan) --}}
        <div class="space-y-6">
            {{-- Penyelenggara & Peserta --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Pihak Terkait</h3>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500 mb-2">Substansi Penyelenggara:</p>
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full">{{ $suratTugas->substansi->nama ?? 'N/A' }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Pegawai yang Bertugas:</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($suratTugas->pegawais as $pegawai)
                            <span class="inline-block bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded-full">{{ $pegawai->nama }}</span>
                        @empty
                            <p class="text-sm text-gray-500 italic">Tidak ada pegawai yang ditugaskan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Penandatangan & Lampiran --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500 mb-2">Disetujui dan Ditandatangani oleh:</p>
                    @if($suratTugas->penandatangan)
                        <div class="p-3 bg-green-50 border border-green-200 rounded">
                            <p class="font-semibold text-green-800">{{ $suratTugas->penandatangan->nama }}</p>
                            <p class="text-xs text-green-600">{{ $suratTugas->penandatangan->jabatan }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Informasi penandatangan tidak tersedia.</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">Lampiran:</p>
                    @if($suratTugas->surattugas)
                        <a href="{{ asset('storage/' . $suratTugas->surattugas) }}" target="_blank" class="flex items-center text-sm text-blue-600 hover:underline hover:text-blue-800 transition duration-200">
                            <i class="fas fa-file-download w-5 text-center mr-3"></i>
                            <span>Unduh Dokumen Surat Tugas</span>
                        </a>
                    @else
                        <p class="text-sm text-gray-500 italic">Tidak ada dokumen lampiran.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
