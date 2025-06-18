<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParafSurat extends Model
{
    use HasFactory;

    protected $table = 'paraf_untuk_surat'; // Nama tabel

    protected $fillable = ['paraf_surat'];

    public function suratTugas()
    {
        return $this->belongsToMany(SuratTugas::class, 'surat_tugas_paraf_surat', 'paraf_surat_id', 'surat_tugas_id');
    }
}
