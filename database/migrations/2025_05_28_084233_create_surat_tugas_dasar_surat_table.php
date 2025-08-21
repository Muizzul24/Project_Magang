<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratTugasDasarSuratTable extends Migration
{
    public function up()
    {
        Schema::create('surat_tugas_dasar_surat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_tugas_id')
                ->constrained();

            $table->foreignId('dasar_surat_id')
                ->constrained('dasar_untuk_surat');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_tugas_dasar_surat');
    }
}
