<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsedDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'user_id',
        'order_id',
        'amount_applied',
    ];

    protected $casts = [
        'amount_applied' => 'decimal:2',
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
} 
