@extends('layouts.app')

@section('title', 'Detail Surat Tugas')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Detail Surat Tugas</h1>
<a href="{{ route('surat_tugas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block mb-4">← Kembali</a>

<div class="bg-white p-6 rounded shadow space-y-3">
    <p><strong>Nomor Surat:</strong> {{ $suratTugas->nomor_surat }}</p>
    <p><strong>Tanggal Surat:</strong> {{ \Carbon\Carbon::parse($suratTugas->tanggal_surat)->format('d-m-Y') }}</p>
    <p><strong>Tujuan:</strong> {{ $suratTugas->tujuan }}</p>
    <p><strong>Substansi:</strong> {{ $suratTugas->substansi->nama ?? '-' }}</p>

    {{-- Dasar Surat --}}
    <div>
        <strong>Dasar Surat:</strong>
        @if($suratTugas->dasarSurat->count() > 0)
            <ul class="list-disc list-inside ml-4 mt-1">
                @foreach ($suratTugas->dasarSurat as $dasar)
                    <li>{{ $dasar->dasar_surat }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 italic">Tidak ada dasar surat.</p>
        @endif
    </div>

    {{-- Paraf Surat --}}
    <div>
        <strong>Paraf Surat:</strong>
        @if($suratTugas->parafSurat->count() > 0)
            <ul class="list-disc list-inside ml-4 mt-1">
                @foreach ($suratTugas->parafSurat as $paraf)
                    <li>{{ $paraf->paraf_surat }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 italic">Tidak ada paraf surat.</p>
        @endif
    </div>

    {{-- Pegawai --}}
    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-2">Pegawai Terkait:</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Nama</th>
                    <th class="py-2 px-4 border-b">NIP</th>
                    <th class="py-2 px-4 border-b">Pangkat/Golongan</th>
                    <th class="py-2 px-4 border-b">Jabatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suratTugas->pegawais as $pegawai)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $pegawai->nama }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->nip }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->pangkat_golongan }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->jabatan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-2 px-4 border-b">Tidak ada pegawai terkait.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- File Surat Tugas --}}
    <div class="mt-4">
        <strong>File Surat Tugas:</strong>
        @if ($suratTugas->surattugas)
            <p>
                <a href="{{ asset('storage/' . $suratTugas->surattugas) }}" class="text-blue-500 hover:underline" target="_blank">Lihat Surat Tugas</a>
            </p>
        @else
            <p class="text-gray-500 italic">Tidak ada file surat tugas.</p>
        @endif
    </div>
</div>
@endsection
