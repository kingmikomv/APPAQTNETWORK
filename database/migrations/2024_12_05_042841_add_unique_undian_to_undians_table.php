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
    Schema::table('undian', function (Blueprint $table) {
        $table->string('unique_undian')->unique()->after('tanggal');
    });
}

public function down()
{
    Schema::table('undian', function (Blueprint $table) {
        $table->dropColumn('unique_undian');
    });
}

};
