<?php

/**
 * Product Model
 *
 * This model represents a product in the e-commerce system.
 * Products can have variants, options, and belong to categories and brands.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'b2b_price',
        'min_order_quantity',
        'max_order_quantity',
        'bulk_pricing',
        'specifications',
        'is_active',
        'is_b2b',
        'requires_approval',
        'approval_notes',
        'is_featured_b2b',
        'featured_until',
        'category_id',
        'brand_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'b2b_price' => 'decimal:2',
        'bulk_pricing' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'is_b2b' => 'boolean',
        'requires_approval' => 'boolean',
        'is_featured_b2b' => 'boolean',
        'featured_until' => 'datetime',
    ];

    // Relationships

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class); // Adjust if BulkPricingTier was meant
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function quoteRequestItems(): HasMany
    {
        return $this->hasMany(QuoteRequestItem::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function primaryImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeB2B($query)
    {
        return $query->where('is_b2b', true);
    }

    public function scopeFeaturedB2B($query)
    {
        return $query->where('is_featured_b2b', true)
            ->where(function ($query) {
                $query->whereNull('featured_until')
                      ->orWhere('featured_until', '>', now());
            });
    }
}
