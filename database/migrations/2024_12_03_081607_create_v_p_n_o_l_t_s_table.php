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
        Schema::create('vpnolt', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->string('namaakun')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('ipaddress')->nullable();

            /**
             * 'unique_id' => $unique->unique_id,
                'namaakun' => $namaakun,
                'username' => $username,
                'password' => $password,
                'ipaddress' => $remoteIp,
             * 
             * 
             */
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
        Schema::dropIfExists('vpnolt');
    }
};
