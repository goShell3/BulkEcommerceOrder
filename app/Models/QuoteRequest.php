/**
 * QuoteRequest Model
 *
 * This model represents a request for a price quote from a B2B customer.
 * Quote requests can be converted into orders once accepted by the customer.
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
 * Class QuoteRequest
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $b2b_account_id
 * @property string $request_notes
 * @property string $status
 * @property int|null $admin_user_id
 * @property string|null $admin_notes
 * @property float|null $quoted_price_total
 * @property \Carbon\Carbon|null $quoted_delivery_date
 * @property array|null $quoted_terms
 * @property \Carbon\Carbon|null $accepted_at
 * @property \Carbon\Carbon|null $rejected_at
 * @property int|null $converted_to_order_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'b2b_account_id',
        'request_notes',
        'status',
        'admin_user_id',
        'admin_notes',
        'quoted_price_total',
        'quoted_delivery_date',
        'quoted_terms',
        'accepted_at',
        'rejected_at',
        'converted_to_order_id',
    ];

    protected $casts = [
        'quoted_price_total' => 'decimal:2',
        'quoted_terms' => 'array',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the user who created this quote request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the B2B account associated with this quote request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function b2bAccount(): BelongsTo
    {
        return $this->belongsTo(B2BAccount::class);
    }

    /**
     * Get the admin user who processed this quote request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the items in this quote request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteRequestItem::class);
    }

    /**
     * Get the order that was created from this quote request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'converted_to_order_id');
    }
} 
