<?php
/**
 * EmailLog Model
 *
 * This model represents a log entry for sent emails in the system.
 * It tracks the status and details of each email sent through the application.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EmailLog
 *
 * @property int $id
 * @property string $recipient_email
 * @property int|null $user_id
 * @property int $email_template_id
 * @property string $subject
 * @property string $body
 * @property string $status
 * @property string|null $error_message
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email',
        'user_id',
        'email_template_id',
        'subject',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the user who received this email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the email template used for this email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
} 
