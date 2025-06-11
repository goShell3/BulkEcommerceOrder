/**
 * ReturnItem Model
 *
 * This model represents an item being returned as part of a return request.
 * It tracks the quantity being returned, reason for return, and refund amount.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ReturnItem
 *
 * @property int $id
 * @property int $return_request_id
 * @property int $order_item_id
 * @property int $quantity
 * @property string $reason
 * @property float $refund_amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'order_item_id',
        'quantity',
        'reason',
        'refund_amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get the return request that owns this item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    /**
     * Get the order item being returned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
} 
