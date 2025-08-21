<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    // Daftar field yang boleh diisi mass assignment
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tujuan',
        'substansi_id',
        'substansi_penandatangan_id',
        'penandatangan_id',
    ];

    protected $casts = [
        'tanggal_surat' => 'date', // <--- ini yang penting
    ];
    
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_surat_tugas');
    }

    public function substansi()
    {
        return $this->belongsTo(Substansi::class, 'substansi_id');
    }

    public function suratTugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'pegawai_surat_tugas', 'pegawai_id', 'surat_tugas_id');
    }

    public function dasarSurat()
    {
        return $this->belongsToMany(DasarSurat::class, 'surat_tugas_dasar_surat');
    }

    public function parafSurat()
    {
        return $this->belongsToMany(ParafSurat::class, 'surat_tugas_paraf_surat');
    }

    public function penandatangan()
    {
        return $this->belongsTo(Pegawai::class, 'penandatangan_id');
    }

    public function agendas()
    {
        // Asumsi nama foreign key di tabel agendas adalah 'surat_tugas_id'
        return $this->hasMany(Agenda::class, 'surat_tugas_id');
    }
}
