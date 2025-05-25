<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order_value',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit_total',
        'usage_limit_per_user',
        'is_active',
        'applies_to_all_products',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'usage_limit_total' => 'integer',
        'usage_limit_per_user' => 'integer',
        'is_active' => 'boolean',
        'applies_to_all_products' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_products')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_categories')
            ->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(UsedDiscount::class);
    }
} 