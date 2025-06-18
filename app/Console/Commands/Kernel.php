<?php

namespace App\Console;

use App\Console\Commands\ArsipAgendaTerlewat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar perintah Artisan yang disediakan oleh aplikasi.
     *
     * @var array
     */
    protected $commands = [
        ArsipAgendaTerlewat::class, // Menambahkan command arsip agenda
    ];

    /**
     * Menentukan penjadwalan tugas aplikasi.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Menjadwalkan command arsip agenda untuk dijalankan setiap hari jam 00:00
        $schedule->command('agenda:arsip-terlewat')->everyMinute();
    }

    /**
     * Mendaftarkan perintah untuk aplikasi.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
