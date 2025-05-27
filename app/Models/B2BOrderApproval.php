<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class B2BOrderApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'requester_user_id',
        'approver_user_id',
        'status',
        'approval_notes',
        'required_threshold',
    ];

    protected $casts = [
        'required_threshold' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
} 
