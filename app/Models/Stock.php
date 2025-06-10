<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'min_order_quantity',
        'max_order_quantity',
        'reserved_quantity',
        'available_quantity',
        'low_stock_threshold',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'min_order_quantity' => 'integer',
        'max_order_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'available_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the stock.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include active stock records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include low stock items.
     */
    public function scopeLowStock($query)
    {
        return $query->where('available_quantity', '<=', 'low_stock_threshold');
    }

    /**
     * Check if the stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if there is enough stock for the requested quantity.
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->available_quantity >= $quantity;
    }

    /**
     * Reserve stock for an order.
     */
    public function reserveStock(int $quantity): bool
    {
        if (!$this->hasEnoughStock($quantity)) {
            return false;
        }

        $this->reserved_quantity += $quantity;
        $this->available_quantity -= $quantity;
        $this->save();

        return true;
    }

    /**
     * Release reserved stock.
     */
    public function releaseStock(int $quantity): bool
    {
        if ($this->reserved_quantity < $quantity) {
            return false;
        }

        $this->reserved_quantity -= $quantity;
        $this->available_quantity += $quantity;
        $this->save();

        return true;
    }
} 