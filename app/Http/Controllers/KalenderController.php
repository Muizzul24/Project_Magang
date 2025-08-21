<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index()
    {
        $query = Agenda::where('arsip', false);

        // Batasi data hanya untuk substansi milik operator atau anggota
        if (in_array(auth()->user()->role, ['operator', 'anggota'])) {
            $query->where('substansi_id', auth()->user()->substansi_id);
        }

        $agendas = $query->get();

        // Ubah data agenda menjadi format yang mudah dibaca oleh kalender
        $events = $agendas->map(function ($agenda) {
            return [
                'title' => $agenda->kegiatan,
                'start' => $agenda->tanggal_mulai->toDateString(), // Format YYYY-MM-DD
                'end'   => $agenda->tanggal_selesai->toDateString(), // Format YYYY-MM-DD
                'url'   => route('agendas.show', $agenda->id),
                'color' => $this->getSubstansiColor($agenda->substansi->nama ?? ''),
            ];
        });

        return view('kalender.index', compact('events'));
    }

    /**
     * Memberikan warna berbeda untuk setiap substansi agar mudah dibedakan di kalender.
     */
    private function getSubstansiColor($substansiNama)
    {
        // Anda bisa menyesuaikan warna ini
        $colors = [
            'DATIN'    => 'bg-blue-100 text-blue-800 border-blue-300',
            'DALAK'    => 'bg-green-100 text-green-800 border-green-300',
            'LP3M'     => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'PROMOSI'  => 'bg-purple-100 text-purple-800 border-purple-300',
            'KESEKRETARIATAN' => 'bg-red-100 text-red-800 border-red-300',
        ];

        return $colors[$substansiNama] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    }
}
