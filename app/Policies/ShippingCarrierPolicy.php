<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ShippingCarrier;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingCarrierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any shipping carriers.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view active shipping carriers
    }

    /**
     * Determine whether the user can view the shipping carrier.
     */
    public function view(User $user, ShippingCarrier $carrier): bool
    {
        return true; // All users can view shipping carrier details
    }

    /**
     * Determine whether the user can create shipping carriers.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the shipping carrier.
     */
    public function update(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the shipping carrier.
     */
    public function delete(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the shipping carrier.
     */
    public function restore(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the shipping carrier.
     */
    public function forceDelete(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage shipping methods for the carrier.
     */
    public function manageMethods(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can configure carrier settings.
     */
    public function configure(User $user, ShippingCarrier $carrier): bool
    {
        return $user->hasRole('admin');
    }
} 
