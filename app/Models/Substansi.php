<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Substansi extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi
    protected $fillable = ['nama'];

    // Relasi: Substansi memiliki banyak disposisi
    public function disposisis()
    {
        return $this->hasMany(Disposisi::class);
    }

    // Relasi: Substansi memiliki banyak pegawai
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
