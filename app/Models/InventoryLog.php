/**
 * InventoryLog Model
 *
 * This model represents a log entry for inventory changes in the system.
 * It tracks all changes to product quantities, including the reason and user who made the change.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryLog
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property int $user_id
 * @property int $quantity_change
 * @property int $new_quantity
 * @property string $type
 * @property string|null $notes
 * @property string|null $reference_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'quantity_change',
        'new_quantity',
        'type',
        'notes',
        'reference_id',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'new_quantity' => 'integer',
    ];

    /**
     * Get the product associated with this inventory log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant associated with this inventory log.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the user who made this inventory change.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 
