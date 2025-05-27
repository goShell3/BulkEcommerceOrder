<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Discount;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any discounts.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view active discounts
    }

    /**
     * Determine whether the user can view the discount.
     */
    public function view(User $user, Discount $discount): bool
    {
        return true; // All users can view discount details
    }

    /**
     * Determine whether the user can create discounts.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the discount.
     */
    public function update(User $user, Discount $discount): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the discount.
     */
    public function delete(User $user, Discount $discount): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the discount.
     */
    public function restore(User $user, Discount $discount): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the discount.
     */
    public function forceDelete(User $user, Discount $discount): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can apply the discount.
     */
    public function apply(User $user, Discount $discount): bool
    {
        return $discount->isActive() && $discount->isValidForUser($user);
    }

    /**
     * Determine whether the user can manage discount conditions.
     */
    public function manageConditions(User $user, Discount $discount): bool
    {
        return $user->hasRole('admin');
    }
} 
