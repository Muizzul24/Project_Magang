<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter 'periode', default 7 hari
        $days = (int) $request->input('periode', 7);

        $startDate = Carbon::today(); // mulai hari ini
        $endDate = Carbon::today()->addDays($days); // sampai hari ke-N

        // Ambil agenda berdasarkan tanggal dan batasi substansi untuk operator/anggota
        $recentAgendas = Agenda::with(['substansi', 'pegawais'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when(in_array(auth()->user()->role, ['operator', 'anggota']), function ($query) {
                $query->where('substansi_id', auth()->user()->substansi_id);
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('dashboard', compact('recentAgendas', 'days'));
    }
}
