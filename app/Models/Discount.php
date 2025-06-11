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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'is_active',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'value' => 'decimal:2',
    ];

    /**
     * Get the products that this discount applies to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Get the categories that this discount applies to.

     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany

     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Check if the discount is currently valid.
     */
    public function isValid(): bool
    {
        $now = now();
        return $this->is_active &&
            $now->between($this->start_date, $this->end_date) &&
            ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if (!$this->isValid() || $orderTotal < $this->min_order_amount) {
            return 0;
        }

        $discountAmount = $this->type === 'percentage'
            ? ($orderTotal * $this->value / 100)
            : $this->value;

        if ($this->max_discount_amount !== null) {
            $discountAmount = min($discountAmount, $this->max_discount_amount);
        }

        return $discountAmount;
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
