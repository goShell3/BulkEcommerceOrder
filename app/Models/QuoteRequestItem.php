<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
} 