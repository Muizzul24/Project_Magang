<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\Substansi;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\SuratTugas;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AgendaPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID'); // Menggunakan lokal Indonesia

        // Mengosongkan tabel untuk PostgreSQL
        DB::statement('TRUNCATE TABLE agendas, pegawais, substansis, users, surat_tugas, agenda_pegawai RESTART IDENTITY CASCADE');
        DB::table('dasar_untuk_surat')->truncate();
        DB::table('paraf_untuk_surat')->truncate();


        // Tambahkan user admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Admin Utama',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'substansi_id' => null,
            ]
        );

        // Buat 5 substansi
        $substansiList = ['DATIN', 'DALAK', 'LP3M', 'PROMOSI', 'KESEKRETARIATAN'];
        foreach ($substansiList as $namaSubstansi) {
            Substansi::create(['nama' => $namaSubstansi]);
        }
        $substansis = Substansi::all();

        // Buat user operator untuk setiap substansi
        foreach ($substansis as $substansi) {
            User::create([
                'nama' => 'Operator ' . $substansi->nama,
                'username' => strtolower($substansi->nama),
                'password' => Hash::make('password'),
                'role' => 'operator',
                'substansi_id' => $substansi->id,
            ]);
        }

        // Buat 50 pegawai dengan substansi random
        for ($i = 1; $i <= 50; $i++) {
            Pegawai::create([
                'nama' => $faker->name,
                'nip' => $faker->unique()->numerify('19##################'),
                'pangkat_golongan' => $faker->randomElement(['III/a', 'III/b', 'III/c', 'IV/a']),
                'jabatan' => 'Staf ' . $faker->jobTitle,
                'substansi_id' => $substansis->random()->id,
            ]);
        }
        $pegawais = Pegawai::all();

        // =============================================
        // PERBAIKAN: Menghapus pembuatan SuratTugas dummy
        // =============================================
        // Blok kode untuk membuat SuratTugas dummy telah dihapus.

        // Buat 150 agenda
        foreach (range(1, 150) as $index) {
            $tanggalMulai = Carbon::instance($faker->dateTimeBetween('-1 month', '+3 months'));
            $durasiAcara = rand(0, 4); 
            $tanggalSelesai = $tanggalMulai->copy()->addDays($durasiAcara);

            $agenda = Agenda::create([
                'substansi_id' => $substansis->random()->id,
                'kegiatan' => 'Rapat ' . $faker->sentence(3),
                'asal_surat' => $faker->company . ' ' . $faker->companySuffix,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'tempat' => 'Hotel ' . $faker->lastName . ', ' . $faker->city,
                'keterangan_agenda' => $faker->paragraph(2),
                'surat' => null,
                // =============================================
                // PERBAIKAN: Menghapus referensi ke surat_tugas_id
                // =============================================
                // 'surat_tugas_id' => null, // Baris ini dihapus
                'arsip' => $tanggalSelesai->isPast(),
            ]);

            // Attach 1-3 pegawai secara random ke agenda
            $agenda->pegawais()->attach(
                $pegawais->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

        // Data Dasar Surat
        $dasarSurat = [
            'Peraturan Daerah Provinsi Jawa Timur Nomor 9 Tahun 2024 tentang Anggaran Pendapatan dan Belanja Daerah Provinsi Jawa Timur Tahun Anggaran 2025 tanggal 31 Desember 2024 LD',
            'Peraturan Gubernur Jawa Timur Nomor 46 Tahun 2024 tentang Penjabaran Anggaran Pendapatan dan Belanja Daerah Provinsi Jawa Timur Tahun Anggaran 2025 tanggal 31 Desember 2024 Nomor 47 Seri E.',
            'Dokumen Pelaksanaan Anggaran (DPA) Dinas Penaman Modal dan Pelayanan Terpadu Satu Pintu Provinsi Jawa Timur Tahun Anggaran 2025 Nomor DPA/A.1/2.18.0.00.0.00.01.0000/001/2025 tanggal 01 Januari 2025.',
        ];
        foreach ($dasarSurat as $dasar) {
            DB::table('dasar_untuk_surat')->insert(['dasar_surat' => $dasar]);
        }

        // Data Paraf Surat
        $parafSurat = [
            'Koordinator Tim Kerja Substansi Pengolahan Data Dan Sistem Informasi Penanaman Modal',
            'Ketua Tim Kerja Verifikasi dan Pengolahan Data Penanaman Modal',
            'Ketua Tim Analisa dan Evaluasi Data Penanaman Modal',
            'Ketua Tim Kerja Analisa dan Evaluasi Data Penanaman Modal',
        ];
        foreach ($parafSurat as $paraf) {
            DB::table('paraf_untuk_surat')->insert(['paraf_surat' => $paraf]);
        }

        $this->command->info('Seeder selesai: Data dummy berhasil dibuat!');
    }
}
