/**
 * Discount Model
 *
 * This model represents a discount or promotion in the e-commerce system.
 * Discounts can be applied to specific products, categories, or all products.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Discount
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property float $value
 * @property float|null $min_order_value
 * @property float|null $max_discount_amount
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property int|null $usage_limit_total
 * @property int|null $usage_limit_per_user
 * @property bool $is_active
 * @property bool $applies_to_all_products
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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

    /**
     * Get the products that this discount applies to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_products')
            ->withTimestamps();
    }

    /**
     * Get the categories that this discount applies to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_categories')
            ->withTimestamps();
    }

    /**
     * Get the usage records for this discount.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usages(): HasMany
    {
        return $this->hasMany(UsedDiscount::class);
    }
} 
