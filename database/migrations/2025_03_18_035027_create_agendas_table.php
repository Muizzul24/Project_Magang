<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->string('asal_surat');
            
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            
            $table->string('tempat');
            $table->text('keterangan_agenda')->nullable();
            
            // Foreign key untuk substansi
            $table->foreignId('substansi_id')->nullable()->constrained('substansis')->onDelete('set null');

            $table->foreignId('surat_tugas_id')
                  ->nullable()
                  ->constrained('surat_tugas')
                  ->onDelete('set null'); // Jika surat tugas dihapus, kolom ini di agenda akan menjadi null
            
            $table->boolean('arsip')->default(false); 
            $table->string('surat')->nullable();
            $table->string('surat_tugas')->nullable(); // Kolom untuk path file (jika masih dipakai)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
