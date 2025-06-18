<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'substansi_id',
        'pegawai_ids',
        'kegiatan',
        'asal_surat',
        'tanggal',
        'tempat',
        'keterangan_agenda',
        'surat',
        'surat_tugas',
    ];

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class);
    }

    public function substansi()
    {
        return $this->belongsTo(Substansi::class, 'substansi_id');
    }

    public function suratTugas()
    {
        return $this->hasOne(SuratTugas::class);
    }

    public function files()
    {
        return $this->hasMany(AgendaFile::class);
    }
}
