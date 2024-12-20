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
    public function up()
    {
        Schema::create('undian', function (Blueprint $table) {
            $table->id();
            $table->string('unique_undian');
            $table->string('site');
            $table->string('hadiah');
            $table->longText('foto');
            $table->date('tanggal');
            $table->string('pemenang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('undian');
    }
};
