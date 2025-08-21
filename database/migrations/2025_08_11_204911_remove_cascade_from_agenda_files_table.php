<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*Schema::table('agenda_files', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama
            // Laravel biasanya menamainya dengan format: namatabel_kolom_foreign
            $table->dropForeign(['agenda_id']);

            // 2. Tambahkan lagi foreign key yang baru tanpa cascade
            $table->foreign('agenda_id')->references('id')->on('agendas');
        });*/
    }

    public function down(): void
    {
        /*Schema::table('agenda_files', function (Blueprint $table) {
            // Kebalikannya untuk rollback: hapus key baru, tambahkan lagi dengan cascade
            $table->dropForeign(['agenda_id']);

            $table->foreign('agenda_id')->references('id')->on('agendas')->cascadeOnDelete();
        });*/
    }
};