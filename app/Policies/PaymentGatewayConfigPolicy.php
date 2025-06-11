<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PaymentGatewayConfig;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentGatewayConfigPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any payment gateway configurations.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the payment gateway configuration.
     */
    public function view(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create payment gateway configurations.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the payment gateway configuration.
     */
    public function update(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the payment gateway configuration.
     */
    public function delete(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the payment gateway configuration.
     */
    public function restore(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the payment gateway configuration.
     */
    public function forceDelete(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can test the payment gateway configuration.
     */
    public function test(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage payment gateway webhooks.
     */
    public function manageWebhooks(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view payment gateway logs.
     */
    public function viewLogs(User $user, PaymentGatewayConfig $config): bool
    {
        return $user->hasRole('admin');
    }
}
