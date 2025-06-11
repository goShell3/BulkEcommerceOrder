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

/**
 * Class Product
 *
 * @property int $id
 * @property int $brand_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property float $price
 * @property int $stock
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**

     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the stock for the product.

     * Get the categories for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Get the order items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**

     * Get the bulk pricing tiers for the product.
     */
    public function bulkPricingTiers(): HasMany

     * Get the product options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options(): HasMany

    {
        return $this->hasMany(BulkPricingTier::class);
    }

    /**
     * Get the discounts that apply to this product.
     */
    public function discounts(): BelongsToMany

     * Get the product variants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants(): HasMany

    {
        return $this->belongsToMany(Discount::class);
    }

    /**

     * Scope a query to only include active products.
     */
    public function scopeActive($query)

     * Get the inventory logs for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryLogs(): HasMany
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include B2B products.
     */
    public function scopeB2B($query)

     * Get the quote request items for the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quoteRequestItems(): HasMany

    {
        return $query->where('is_b2b', true);
    }

    /**

     * Scope a query to only include featured B2B products.
     */
    public function scopeFeaturedB2B($query)

     * Get the discounts associated with the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts(): HasMany

    {
        return $query->where('is_featured_b2b', true)
            ->where(function ($query) {
                $query->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }
} 
