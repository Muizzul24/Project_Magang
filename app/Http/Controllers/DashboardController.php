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

        $startDate = Carbon::today(); // Mulai hari ini
        $endDate = Carbon::today()->addDays($days); // Sampai hari ke-N
        
        $recentAgendas = Agenda::with(['substansi', 'pegawais'])
            // Cari agenda yang rentang waktunya bersinggungan dengan periode dashboard.
            // Kondisi: Tanggal mulai agenda harus sebelum atau sama dengan tanggal akhir periode,
            // DAN tanggal selesai agenda harus setelah atau sama dengan tanggal mulai periode.
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_selesai', '>=', $startDate);
            })
            ->where('arsip', false) // Tambahan: Pastikan hanya agenda yang belum diarsip
            ->when(in_array(auth()->user()->role, ['operator', 'anggota']), function ($query) {
                $query->where('substansi_id', auth()->user()->substansi_id);
            })
            // Urutkan berdasarkan tanggal mulai agenda
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        return view('dashboard', compact('recentAgendas', 'days'));
    }
}
