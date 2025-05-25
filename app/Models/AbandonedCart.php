<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'cart_data',
        'last_activity_at',
        'reminders_sent_count',
        'converted_to_order_id',
    ];

    protected $casts = [
        'cart_data' => 'array',
        'last_activity_at' => 'datetime',
        'reminders_sent_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_to_order_id');
    }
} 