<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratTugasTable extends Migration
{
    public function up()
    {
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->foreignId('substansi_id')->constrained('substansis');
            $table->text('tujuan')->nullable();
            $table->string('surattugas')->nullable();
            $table->foreignId('substansi_penandatangan_id')->nullable()->constrained('substansis')->onDelete('set null');
            $table->foreignId('penandatangan_id')->nullable()->constrained('pegawais')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas');
    }
}
