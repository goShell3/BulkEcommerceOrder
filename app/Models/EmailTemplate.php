/**
 * EmailTemplate Model
 *
 * This model represents an email template that can be used to send standardized emails.
 * Templates define the subject and body structure for different types of system emails.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class EmailTemplate
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $body
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'body',
        'type',
    ];

    /**
     * Get the email logs that used this template.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }
} 
