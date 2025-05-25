<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ShippingMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingMethodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any shipping methods.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view active shipping methods
    }

    /**
     * Determine whether the user can view the shipping method.
     */
    public function view(User $user, ShippingMethod $method): bool
    {
        return true; // All users can view shipping method details
    }

    /**
     * Determine whether the user can create shipping methods.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the shipping method.
     */
    public function update(User $user, ShippingMethod $method): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the shipping method.
     */
    public function delete(User $user, ShippingMethod $method): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the shipping method.
     */
    public function restore(User $user, ShippingMethod $method): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the shipping method.
     */
    public function forceDelete(User $user, ShippingMethod $method): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can calculate shipping costs.
     */
    public function calculateCost(User $user, ShippingMethod $method): bool
    {
        return true; // All users can calculate shipping costs
    }

    /**
     * Determine whether the user can manage shipping method conditions.
     */
    public function manageConditions(User $user, ShippingMethod $method): bool
    {
        return $user->hasRole('admin');
    }
} 