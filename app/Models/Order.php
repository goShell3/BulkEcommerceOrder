/**
 * Order Model
 *
 * This model represents a customer order in the e-commerce system.
 * It manages the order lifecycle, including status changes, returns, and payments.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Order
 *
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property float $total
 * @property array $shipping_address
 * @property int|null $shipping_address_id
 * @property int|null $billing_address_id
 * @property int|null $quote_request_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the return requests for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    /**
     * Check if the order can be cancelled.
     *
     * @return bool
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']) && 
               !$this->returnRequests()->where('status', '!=', 'rejected')->exists();
    }

    /**
     * Check if the order can be returned.
     *
     * @return bool
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
     *
     * @return void
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

    /**
     * Get the order history entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    /**
     * Get the payment transactions for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the shipping address for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class, 'shipping_address_id');
    }

    /**
     * Get the billing address for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class, 'billing_address_id');
    }

    /**
     * Get the quote request associated with this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * Get the B2B order approval record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function b2bOrderApproval(): HasOne
    {
        return $this->hasOne(B2BOrderApproval::class);
    }
} 
