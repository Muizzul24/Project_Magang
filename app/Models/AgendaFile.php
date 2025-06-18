<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'nama_file',
        'path',
    ];

    /**
     * Relasi ke model Agenda
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
