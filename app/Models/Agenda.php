<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'substansi_id',
        'kegiatan',
        'asal_surat',
        'tanggal_mulai', // Diperbarui
        'tanggal_selesai', // Diperbarui
        'tempat',
        'keterangan_agenda',
        'surat',
        'surat_tugas',
        'arsip', // Menambahkan 'arsip' agar bisa di-update massal jika perlu
        'surat_tugas_id', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class);
    }

    public function substansi()
    {
        return $this->belongsTo(Substansi::class, 'substansi_id');
    }

    public function files()
    {
        return $this->hasMany(AgendaFile::class);
    }

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugas::class, 'surat_tugas_id');
    }
}
