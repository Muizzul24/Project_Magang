<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_pegawai', function (Blueprint $table) {
            $table->id(); // Membuat primary key
            $table->unsignedBigInteger('agenda_id');
            $table->unsignedBigInteger('pegawai_id');
            $table->timestamps();

            // Menambahkan foreign key
            $table->foreign('agenda_id')->references('id')->on('agendas')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            
            // Menambahkan constraint unique
            $table->unique(['agenda_id', 'pegawai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_pegawai'); // Hapus tabel agenda_pegawai saat rollback
    }
};
