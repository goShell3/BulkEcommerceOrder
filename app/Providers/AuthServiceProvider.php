<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Discount;
use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use App\Models\PaymentGatewayConfig;
use App\Policies\OrderPolicy;
use App\Policies\ReturnRequestPolicy;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\BrandPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\ShippingCarrierPolicy;
use App\Policies\ShippingMethodPolicy;
use App\Policies\PaymentGatewayConfigPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model => Policy mappings
        Order::class => OrderPolicy::class,
        ReturnRequest::class => ReturnRequestPolicy::class,
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Discount::class => DiscountPolicy::class,
        ShippingCarrier::class => ShippingCarrierPolicy::class,
        ShippingMethod::class => ShippingMethodPolicy::class,
        PaymentGatewayConfig::class => PaymentGatewayConfigPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register Gates for role-based authorization
        Gate::define(
            'is-admin', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'is-b2b', function (User $user) {
                return $user->hasRole('b2b');
            }
        );

        // Register Gates for resource ownership
        Gate::define(
            'manage-order', function (User $user, Order $order) {
                return $user->id === $order->user_id || $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-return', function (User $user, ReturnRequest $returnRequest) {
                return $user->id === $returnRequest->user_id || $user->hasRole('admin');
            }
        );

        // Register Gates for resource management
        Gate::define(
            'manage-products', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-categories', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-brands', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-discounts', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-shipping', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        Gate::define(
            'manage-payment-gateways', function (User $user) {
                return $user->hasRole('admin');
            }
        );

        // Register Gates for B2B specific actions
        Gate::define(
            'view-b2b-prices', function (User $user) {
                return $user->hasRole(['admin', 'b2b']);
            }
        );

        Gate::define(
            'manage-b2b-orders', function (User $user) {
                return $user->hasRole(['admin', 'b2b']);
            }
        );
    }
} 
