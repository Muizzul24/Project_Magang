<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DasarSurat extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk menghindari error pencarian tabel dasar_surats
    protected $table = 'dasar_untuk_surat';

    protected $fillable = ['dasar_surat'];

    public function suratTugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'surat_tugas_dasar_surat', 'dasar_surat_id', 'surat_tugas_id');
    }
}
