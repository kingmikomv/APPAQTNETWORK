<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->string('external_id')->unique(); // ID unik untuk invoice
            $table->decimal('amount', 15, 2); // Jumlah pembayaran
            $table->string('payer_email'); // Email pembayar
            $table->string('description'); // Deskripsi pembayaran
            $table->enum('status', ['PENDING', 'PAID', 'FAILED'])->default('PENDING'); // Status invoice
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
