<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParafUntukSuratTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('paraf_untuk_surat', function (Blueprint $table) {
            $table->id();
            $table->text('paraf_surat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('paraf_untuk_surat');
    }
}
