/**
 * OrderAddress Model
 *
 * This model represents an address associated with an order.
 * It can be either a shipping or billing address and may be linked to a user's saved address.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderAddress
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $user_address_id
 * @property string $type
 * @property string $full_name
 * @property string|null $company_name
 * @property string $address_line1
 * @property string|null $address_line2
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $country
 * @property string $phone_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_address_id',
        'type',
        'full_name',
        'company_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
    ];

    /**
     * Get the order that owns this address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user's saved address that this order address is based on.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class);
    }
} 
