<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ArsipAgendaTerlewat extends Command
{
    /**
     * Nama dan signature command.
     *
     * @var string
     */
    protected $signature = 'agenda:arsip-terlewat';

    /**
     * Deskripsi command.
     *
     * @var string
     */
    protected $description = 'Mengarsipkan agenda yang sudah lewat tanggal secara otomatis';

    /**
     * Jalankan command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

        // Catat waktu command dijalankan ke file log
        Log::info('[ARSIP] Command dijalankan pada: ' . now());

        // Update semua agenda yang tanggalnya sudah lewat dan belum diarsipkan
        $updated = Agenda::where('tanggal', '<', $today)
            ->where('arsip', false)
            ->update(['arsip' => true]);

        // Catat hasil update
        Log::info("[ARSIP] Jumlah agenda yang diarsipkan: {$updated}");

        $this->info("Berhasil mengarsipkan $updated agenda yang sudah lewat tanggal.");
        return 0;
    }
}
