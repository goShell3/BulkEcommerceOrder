<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Events\ReturnRequestCreated;
use App\Events\ReturnRequestStatusUpdated;
use App\Events\ProductStockUpdated;
use App\Events\PaymentProcessed;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\SendReturnRequestConfirmation;
use App\Listeners\SendReturnRequestStatusNotification;
use App\Listeners\UpdateProductInventory;
use App\Listeners\SendPaymentConfirmation;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Order Events
        OrderCreated::class => [
            SendOrderConfirmation::class,
            UpdateProductInventory::class,
        ],
        OrderStatusUpdated::class => [
            SendOrderStatusNotification::class,
        ],

        // Return Request Events
        ReturnRequestCreated::class => [
            SendReturnRequestConfirmation::class,
        ],
        ReturnRequestStatusUpdated::class => [
            SendReturnRequestStatusNotification::class,
        ],

        // Product Events
        ProductStockUpdated::class => [
            UpdateProductInventory::class,
        ],

        // Payment Events
        PaymentProcessed::class => [
            SendPaymentConfirmation::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register event subscribers
        Event::subscribe(\App\Listeners\OrderEventSubscriber::class);
        Event::subscribe(\App\Listeners\ReturnRequestEventSubscriber::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 
