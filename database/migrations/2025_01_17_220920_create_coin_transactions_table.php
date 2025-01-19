<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Relasi ke tabel users
            $table->integer('coin_amount'); // Jumlah coin yang dibeli
            $table->decimal('price', 10); // Harga pembelian
            $table->string('status')->default('pending'); // Status transaksi (pending/complete/failed)
            $table->longText('invoice_url')->nullable(); // Catatan transaksi
            $table->string('external_id')->unique()->nullable(); // UUID untuk transaksi

            $table->timestamps();
            
            // Foreign key constraint (Opsional)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_transactions');
    }
}
