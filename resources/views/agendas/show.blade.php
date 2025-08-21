@extends('layouts.app')

@section('title', 'Detail Agenda')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    
    {{-- Header Halaman dengan Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-2xl font-bold text-gray-800">{{ $agenda->kegiatan }}</h1>
            <p class="text-sm text-gray-500 mt-1">Detail agenda kegiatan</p>
        </div>
        <div class="flex items-center space-x-2 mt-4 sm:mt-0">
            <a href="{{ route('agendas.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded text-sm transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'operator']))
                <a href="{{ route('agendas.edit', $agenda->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-4 py-2 rounded text-sm transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Layout Grid untuk Konten --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Utama (Kiri) --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Detail Informasi</h3>
                
                <dl class="space-y-4">
                    <div class="flex items-start">
                        <dt class="w-1/3 text-base font-medium text-gray-500 flex items-center">
                            <i class="fas fa-calendar-alt w-5 text-center mr-3 text-blue-500"></i>
                            <span>Rentang Tanggal</span>
                        </dt>
                        <dd class="w-2/3 text-base text-gray-900">
                            @if($agenda->tanggal_mulai->eq($agenda->tanggal_selesai))
                                {{ $agenda->tanggal_mulai->isoFormat('dddd, D MMMM Y') }}
                            @else
                                {{ $agenda->tanggal_mulai->isoFormat('dddd, D MMMM Y') }} <br>
                                <span class="font-semibold">sampai</span><br>
                                {{ $agenda->tanggal_selesai->isoFormat('dddd, D MMMM Y') }}
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-start">
                        <dt class="w-1/3 text-base font-medium text-gray-500 flex items-center">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-3 text-green-500"></i>
                            <span>Tempat</span>
                        </dt>
                        <dd class="w-2/3 text-base text-gray-900">{{ $agenda->tempat }}</dd>
                    </div>

                    <div class="flex items-start">
                        <dt class="w-1/3 text-base font-medium text-gray-500 flex items-center">
                            <i class="fas fa-building w-5 text-center mr-3 text-purple-500"></i>
                            <span>Asal Surat</span>
                        </dt>
                        <dd class="w-2/3 text-base text-gray-900">{{ $agenda->asal_surat }}</dd>
                    </div>

                    <div class="flex items-start">
                        <dt class="w-1/3 text-base font-medium text-gray-500 flex items-center">
                            <i class="fas fa-info-circle w-5 text-center mr-3 text-gray-500"></i>
                            <span>Keterangan</span>
                        </dt>
                        <dd class="w-2/3 text-base text-gray-900 whitespace-pre-wrap">{{ $agenda->keterangan_agenda }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Kolom Samping (Kanan) --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Substansi & Peserta</h3>
                <div class="mb-4">
                    <p class="text-base font-medium text-gray-500 mb-2">Diselenggarakan oleh:</p>
                    <span class="inline-block bg-indigo-100 text-indigo-800 text-base font-semibold px-3 py-1 rounded-full">{{ $agenda->substansi->nama ?? 'N/A' }}</span>
                </div>
                <div>
                    <p class="text-base font-medium text-gray-500 mb-2">Pegawai yang Bertugas:</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($agenda->pegawais as $pegawai)
                            <span class="inline-block bg-gray-200 text-gray-700 text-sm font-medium px-2 py-1 rounded-full">{{ $pegawai->nama }}</span>
                        @empty
                            <p class="text-base text-gray-500 italic">Tidak ada pegawai yang ditugaskan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Lampiran</h3>
                <ul class="space-y-2">
                    @if($agenda->surat)
                        @php $files = explode(',', $agenda->surat); @endphp
                        @foreach($files as $index => $file)
                            @if(!empty($file))
                            <li>
                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="flex items-center text-base text-blue-600 hover:underline hover:text-blue-800 transition duration-200">
                                    <i class="fas fa-file-alt w-5 text-center mr-3"></i>
                                    <span>Lihat Surat Lampiran {{ $index + 1 }}</span>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    @endif

                    @if($agenda->surat_tugas)
                        <li>
                            <a href="{{ asset('storage/' . $agenda->surat_tugas) }}" target="_blank" class="flex items-center text-base text-green-600 hover:underline hover:text-green-800 transition duration-200">
                                <i class="fas fa-file-signature w-5 text-center mr-3"></i>
                                <span>Lihat Surat Tugas Terkait</span>
                            </a>
                        </li>
                    @endif

                    @if(!$agenda->surat && !$agenda->surat_tugas)
                        <p class="text-base text-gray-500 italic">Tidak ada lampiran.</p>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
