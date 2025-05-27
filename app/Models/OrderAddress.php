<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_address_id',
        'type',
        'full_name',
        'company_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
} 
