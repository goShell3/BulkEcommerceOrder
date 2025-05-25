<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any brands.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view brands
    }

    /**
     * Determine whether the user can view the brand.
     */
    public function view(User $user, Brand $brand): bool
    {
        return true; // All users can view brand details
    }

    /**
     * Determine whether the user can create brands.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the brand.
     */
    public function update(User $user, Brand $brand): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the brand.
     */
    public function delete(User $user, Brand $brand): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the brand.
     */
    public function restore(User $user, Brand $brand): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the brand.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage brand products.
     */
    public function manageProducts(User $user, Brand $brand): bool
    {
        return $user->hasRole('admin');
    }
} 