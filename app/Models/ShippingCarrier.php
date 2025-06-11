<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCarrier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'tracking_url'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class, 'carrier_id');
    }
} 