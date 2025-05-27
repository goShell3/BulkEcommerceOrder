<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'shipping_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipping_address' => 'array',
        'total' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the return requests for the order.
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']) && 
               !$this->returnRequests()->where('status', '!=', 'rejected')->exists();
    }

    /**
     * Check if the order can be returned.
     */
    public function canBeReturned(): bool
    {
        // Can only return delivered orders within 30 days
        if ($this->status !== 'delivered') {
            return false;
        }

        $deliveryDate = $this->updated_at;
        $returnDeadline = now()->subDays(30);

        return $deliveryDate->isAfter($returnDeadline) && 
               !$this->returnRequests()->where('status', '!=', 'rejected')->exists();
    }

    /**
     * Calculate the total amount for the order.
     */
    public function calculateTotal(): void
    {
        $total = $this->items->sum(
            function ($item) {
                return $item->price * $item->quantity;
            }
        );

        $this->update(['total' => $total]);
    }

    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class, 'billing_address_id');
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function b2bOrderApproval(): HasOne
    {
        return $this->hasOne(B2BOrderApproval::class);
    }
} 
