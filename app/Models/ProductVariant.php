/**
 * ProductVariant Model
 *
 * This model represents a specific variant of a product (e.g., "Red T-Shirt in Size L").
 * Variants are created by combining different option values and can have their own pricing and inventory.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ProductVariant
 *
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property string $price_adjustment_type
 * @property float $price_adjustment_value
 * @property float $price
 * @property int $quantity
 * @property string|null $image_url
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price_adjustment_type',
        'price_adjustment_value',
        'price',
        'quantity',
        'image_url',
        'status',
    ];

    protected $casts = [
        'price_adjustment_value' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the product that owns this variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the option values associated with this variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(ProductOptionValue::class, 'product_variant_options')
            ->withTimestamps();
    }

    /**
     * Get the inventory logs for this variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Get the order items for this variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the quote request items for this variant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quoteRequestItems(): HasMany
    {
        return $this->hasMany(QuoteRequestItem::class);
    }
} 
