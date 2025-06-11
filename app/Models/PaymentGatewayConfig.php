<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'config',
        'supported_currencies',
        'supported_payment_methods',
        'processing_fee',
        'processing_fee_type'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'supported_currencies' => 'array',
        'supported_payment_methods' => 'array',
        'processing_fee' => 'decimal:2'
    ];
} 