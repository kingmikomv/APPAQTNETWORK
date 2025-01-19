<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model
    protected $table = 'invoices';

    // Tentukan kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'external_id',
        'amount',
        'payer_email',
        'description',
        'status',
    ];

    // Tentukan kolom yang tidak boleh diubah (mass assignable)
    protected $guarded = [];

    // Tentukan kolom yang harus diformat
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Jika perlu, tambahkan metode lain untuk memanipulasi data
}
