<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'carrier_id',
        'is_active',
        'base_price',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2'
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id');
    }
}
