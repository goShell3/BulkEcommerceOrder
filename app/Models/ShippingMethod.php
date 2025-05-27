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
        'description',
        'carrier_id',
        'service_code',
        'cost_calculation_type',
        'cost_formula',
        'estimated_delivery_days',
        'is_active',
    ];

    protected $casts = [
        'cost_formula' => 'array',
        'estimated_delivery_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id');
    }
}
