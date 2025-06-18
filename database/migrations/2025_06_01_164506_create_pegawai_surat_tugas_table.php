<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaiSuratTugasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai_surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->foreignId('surat_tugas_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Optional: untuk mencegah duplikat pegawai per surat tugas
            $table->unique(['pegawai_id', 'surat_tugas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_surat_tugas');
    }
}
