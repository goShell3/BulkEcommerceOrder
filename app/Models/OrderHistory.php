/**
 * OrderHistory Model
 *
 * This model represents a history entry for an order's status changes.
 * It tracks who made the change, when it was made, and any associated notes.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderHistory
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $old_status
 * @property string $new_status
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'old_status',
        'new_status',
        'notes',
    ];

    /**
     * Get the order that owns this history entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who made this status change.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 
