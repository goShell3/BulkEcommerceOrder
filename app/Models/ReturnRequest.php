<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'reason',
        'description',
        'status',
    ];

    /**
     * Get the order that owns the return request.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if the return request can be updated to the given status.
     */
    public function canBeUpdatedTo(string $status): bool
    {
        // Define valid status transitions
        $validTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['completed'],
            'rejected' => [],
            'completed' => [],
        ];

        // Check if the current status exists and if the new status is a valid transition
        return isset($validTransitions[$this->status]) && 
               in_array($status, $validTransitions[$this->status]);
    }

    /**
     * Scope a query to only include pending return requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved return requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected return requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include completed return requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
} 
