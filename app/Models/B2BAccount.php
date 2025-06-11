<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class B2BAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person_user_id',
        'status',
        'credit_limit',
        'payment_terms',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];

    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_person_user_id');
    }

    public function accountUsers(): HasMany
    {
        return $this->hasMany(AccountUser::class);
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }
}
