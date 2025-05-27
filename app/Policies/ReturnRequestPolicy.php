<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReturnRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReturnRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any return requests.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their return requests
    }

    /**
     * Determine whether the user can view the return request.
     */
    public function view(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->id === $returnRequest->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create return requests.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create return requests
    }

    /**
     * Determine whether the user can update the return request.
     */
    public function update(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the return request.
     */
    public function delete(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the return request status.
     */
    public function updateStatus(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can process the return request refund.
     */
    public function processRefund(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->hasRole('admin') && $returnRequest->status === 'approved';
    }

    /**
     * Determine whether the user can process the return request replacement.
     */
    public function processReplacement(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->hasRole('admin') && $returnRequest->status === 'approved';
    }
} 
