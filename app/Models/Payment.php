<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'amount_cents',
        'payment_method',
        'status',
        'transaction_id',
    ];
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
