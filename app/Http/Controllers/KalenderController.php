<?php

namespace App\Http\Controllers;

use App\Models\Agenda; // Sesuaikan dengan model agenda Anda
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index()
    {
        $events = Agenda::all()->map(function($agenda) {
            return [
                'date' => $agenda->tanggal, // sesuaikan nama kolom tanggal
                'title' => $agenda->kegiatan, // sesuaikan nama kolom kegiatan
            ];
        });

        // Kirim data event ke view dalam format JSON
        return view('kalender.index', [
            'events' => $events->groupBy('date')->map(function($items) {
                return $items->toArray();
            }),
        ]);
    }
}
