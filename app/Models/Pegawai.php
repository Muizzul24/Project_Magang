<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'substansi_id',
        'nama',
        'nip',
        'pangkat_golongan',
        'jabatan',
    ];

    public function agendas()
    {
        return $this->belongsToMany(Agenda::class);
    }

    public function suratTugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'pegawai_surat_tugas');
    }

    public function substansi()
    {
        return $this->belongsTo(Substansi::class);
    }

    public function suratTugasDitandatangani()
    {
        // Asumsi nama foreign key di tabel surat_tugas adalah 'penandatangan_id'
        return $this->hasMany(SuratTugas::class, 'penandatangan_id');
    }
}
