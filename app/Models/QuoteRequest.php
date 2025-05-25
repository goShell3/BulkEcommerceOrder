<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'b2b_account_id',
        'request_notes',
        'status',
        'admin_user_id',
        'admin_notes',
        'quoted_price_total',
        'quoted_delivery_date',
        'quoted_terms',
        'accepted_at',
        'rejected_at',
        'converted_to_order_id',
    ];

    protected $casts = [
        'quoted_price_total' => 'decimal:2',
        'quoted_terms' => 'array',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function b2bAccount(): BelongsTo
    {
        return $this->belongsTo(B2BAccount::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteRequestItem::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'converted_to_order_id');
    }
} 