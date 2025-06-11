<?php
/**
 * QuoteRequestItem Model
 *
 * This model represents an item within a quote request.
 * It stores the product details and quantities requested by the customer, along with the proposed pricing.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class QuoteRequestItem
 *
 * @property int $id
 * @property int $quote_request_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property int $requested_quantity
 * @property int|null $proposed_quantity
 * @property float|null $proposed_unit_price
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class QuoteRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'product_id',
        'product_variant_id',
        'requested_quantity',
        'proposed_quantity',
        'proposed_unit_price',
        'notes',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'proposed_quantity' => 'integer',
        'proposed_unit_price' => 'decimal:2',
    ];

    /**
     * Get the quote request that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * Get the product associated with this quote request item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant associated with this quote request item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
} 
