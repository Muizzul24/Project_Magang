@extends('layouts.app')

@section('title', 'Detail Agenda')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Detail Agenda</h1>
<a href="{{ route('agendas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block mb-4">← Kembali</a>

<div class="bg-white p-6 rounded shadow">
    <p><strong>Kegiatan:</strong> {{ $agenda->kegiatan }}</p>
    <p><strong>Asal Surat:</strong> {{ $agenda->asal_surat }}</p>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($agenda->tanggal)->format('d-m-Y') }}</p>
    <p><strong>Tempat:</strong> {{ $agenda->tempat }}</p>
    <p><strong>Substansi:</strong> {{ $agenda->substansi->nama ?? '-' }}</p>
    <p><strong>Nama Disposisi:</strong> {{ $agenda->nama_disposisi }}</p>
    <p><strong>Keterangan:</strong> {{ $agenda->keterangan_agenda }}</p>
    <p><strong>Surat:</strong>
        @if ($agenda->surat)
            <a href="{{ asset('storage/' . $agenda->surat) }}" class="text-blue-500 hover:underline" target="_blank">Lihat Surat</a>
        @else
            Tidak ada surat
        @endif
    </p>

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-2">Pegawai Terlibat:</h2>
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
                @forelse ($agenda->pegawais as $pegawai)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $pegawai->nama }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->nip }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->pangkat_golongan }}</td>
                        <td class="py-2 px-4 border-b">{{ $pegawai->jabatan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-2 px-4 border-b">Tidak ada pegawai terlibat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
