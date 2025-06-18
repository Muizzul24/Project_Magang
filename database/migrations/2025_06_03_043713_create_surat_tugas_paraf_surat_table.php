<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratTugasParafSuratTable extends Migration
{
    public function up()
    {
        Schema::create('surat_tugas_paraf_surat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_tugas_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('paraf_surat_id')
                ->constrained('paraf_untuk_surat') // Secara eksplisit refer ke tabel dasar_surat
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas_paraf_surat');
    }
}
