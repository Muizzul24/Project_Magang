<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\Substansi;
use App\Models\Pegawai;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AgendaPegawaiSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Tambahkan user admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('admin'), // enkripsi password
                'role' => 'admin',
                'substansi_id' => null,
            ]
        );

        // Buat 5 substansi tetap jika belum ada
        $substansiList = ['DATIN', 'DALAK', 'LP3M', 'PROMOSI', 'KESRETARIAT'];

        foreach ($substansiList as $namaSubstansi) {
            Substansi::firstOrCreate(['nama' => $namaSubstansi]);
        }

        $substansis = Substansi::all();

        // Buat 50 pegawai dengan substansi random
        for ($i = 1; $i <= 50; $i++) {
            Pegawai::create([
                'nama' => 'Pegawai ' . $i,
                'nip' => 'NIP' . rand(1000, 9999),
                'pangkat_golongan' => 'Golongan ' . rand(1, 4),
                'jabatan' => 'Jabatan ' . $i,
                'substansi_id' => $substansis->random()->id,
            ]);
        }

        $pegawais = Pegawai::all();

        // Buat 150 agenda dengan tanggal masa depan dan attach pegawai random
        $startDate = now()->addDay();

        foreach (range(1, 150) as $index) {
            $agendaDate = $startDate->copy()->addDays($index);

            $agenda = Agenda::create([
                'substansi_id' => $substansis->random()->id,
                'kegiatan' => $faker->sentence,
                'asal_surat' => $faker->company,
                'tanggal' => $agendaDate,
                'tempat' => $faker->city,
                'keterangan_agenda' => $faker->paragraph,
                'surat' => null,
            ]);

            // Attach 1-3 pegawai secara random ke agenda
            $agenda->pegawais()->attach(
                $pegawais->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

        // === Tambahan untuk data dasar_surat ===
        $dasarSurat = [
            'Peraturan Daerah Provinsi Jawa Timur Nomor 9 Tahun 2024 tentang Anggaran Pendapatan dan Belanja Daerah Provinsi Jawa Timur Tahun Anggaran 2025 tanggal 31 Desember 2024 LD',
            'Peraturan Gubernur Jawa Timur Nomor 46 Tahun 2024 tentang Penjabaran Anggaran Pendapatan dan Belanja Daerah Provinsi Jawa Timur Tahun Anggaran 2025 tanggal 31 Desember 2024 Nomor 47 Seri E.',
            'Dokumen Pelaksanaan Anggaran (DPA) Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Provinsi Jawa Timur Tahun Anggaran 2025 Nomor DPA/A.1/2.18.0.00.0.00.01.0000/001/2025 tanggal 01 Januari 2025.',
        ];

        foreach ($dasarSurat as $dasar) {
            DB::table('dasar_untuk_surat')->updateOrInsert(
                ['dasar_surat' => $dasar]
            );
        }

        // === Tambahan untuk data paraf_surat ===
        $parafSurat = [
            'Koordinator Tim Kerja Substansi Pengolahan Data Dan Sistem Informasi Penanaman Modal',
            'Ketua Tim Kerja Verifikasi dan Pengolahan Data Penanaman Modal',
            'Ketua Tim Analisa dan Evaluasi Data Penanaman Modal',
            'Ketua Tim Kerja Analisa dan Evaluasi Data Penanaman Modal',
        ];

        foreach ($parafSurat as $paraf) {
            DB::table('paraf_untuk_surat')->updateOrInsert(
                ['paraf_surat' => $paraf]
            );
        }

        $this->command->info('Seeder selesai: User admin, 5 substansi, 50 pegawai, 150 agenda, data dasar surat dan paraf surat berhasil dibuat!');
    }
}
