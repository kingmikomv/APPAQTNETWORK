<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coin_amount',
        'price',
        'status',
        'external_id',
        'invoice_url',
        'payment_method',
        'payment_channel',
        'ewallet_type',
        'payment_source',
        'paid_at',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
