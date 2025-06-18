<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->string('asal_surat');
            $table->date('tanggal');
            $table->string('tempat');
            $table->text('keterangan_agenda')->nullable();
            $table->unsignedBigInteger('substansi_id')->nullable()->after('id');
            $table->foreign('substansi_id')->references('id')->on('substansis')->onDelete('set null');// Foreign key optional:
            $table->boolean('arsip')->default(false); // Default adalah false (belum diarsipkan) 
            $table->string('surat')->nullable(); // Ubah tipe data dan tambahkan nullable
            $table->string('surat_tugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};