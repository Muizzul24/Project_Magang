@extends('layouts.app')

@section('title', 'Arsip Agenda')

@section('content')
<h2 class="text-2xl font-bold mb-4">Arsip Agenda</h2>

<form action="{{ route('agendas.arsip') }}" method="POST" onsubmit="return confirm('Pindahkan semua agenda yang tanggalnya sudah lewat ke arsip?')">
    @csrf
    <button type="submit" class="mb-4 px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
        Arsipkan Agenda yang Terlewat
    </button>
</form>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">No</th>
                <th class="py-2 px-4 border-b">Kegiatan</th>
                <th class="py-2 px-4 border-b">Asal Surat</th>
                <th class="py-2 px-4 border-b">Tanggal</th>
                <th class="py-2 px-4 border-b">Tempat</th>
                <th class="py-2 px-4 border-b">Substansi</th>
                <th class="py-2 px-4 border-b">Nama Pegawai</th>
                <th class="py-2 px-4 border-b">Keterangan</th>
                <th class="py-2 px-4 border-b">Surat</th>
            </tr>
        </thead>
        <tbody>
            @if($agendaArsip->count() > 0)
                @foreach($agendaArsip as $index => $agenda)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $agenda->kegiatan }}</td>
                        <td class="py-2 px-4 border-b">{{ $agenda->asal_surat }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $agenda->tempat }}</td>
                        <td>{{ $agenda->substansi->nama ?? '-' }}</td>
                        <td class="py-2 px-4 border-b">
                            @foreach ($agenda->pegawais as $pegawai)
                                <span class="inline-block bg-gray-200 text-sm text-gray-700 rounded px-2 py-1 mr-1 mb-1">{{ $pegawai->nama }}</span>
                            @endforeach
                        </td>
                        <td class="py-2 px-4 border-b">{{ $agenda->keterangan_agenda }}</td>
                        <td class="py-2 px-4 border-b">
                            @if($agenda->surat)
                                <a href="{{ asset('storage/' . $agenda->surat) }}" target="_blank" class="text-blue-500 hover:text-blue-700">Lihat Surat</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center py-2 px-4 border-b">Tidak ada agenda yang diarsipkan.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
