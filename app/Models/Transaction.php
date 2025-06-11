<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'type',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
