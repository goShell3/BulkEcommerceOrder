<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'b2b_account_id',
        'user_id',
        'role_id',
        'status',
    ];

    public function b2bAccount(): BelongsTo
    {
        return $this->belongsTo(B2BAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
