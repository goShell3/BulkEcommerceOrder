<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Events\OrderCancelled;
use App\Events\OrderShipped;
use App\Events\OrderDelivered;
use App\Events\OrderRefunded;
use App\Services\NotificationService;
use App\Services\InventoryService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Log;

class OrderEventSubscriber
{
    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * The inventory service instance.
     *
     * @var \App\Services\InventoryService
     */
    protected $inventoryService;

    /**
     * The payment service instance.
     *
     * @var \App\Services\PaymentService
     */
    protected $paymentService;

    /**
     * The shipping service instance.
     *
     * @var \App\Services\ShippingService
     */
    protected $shippingService;

    /**
     * Create the event listener.
     *
     * @param  \App\Services\NotificationService $notificationService
     * @param  \App\Services\InventoryService    $inventoryService
     * @param  \App\Services\PaymentService      $paymentService
     * @param  \App\Services\ShippingService     $shippingService
     * @return void
     */
    public function __construct(
        NotificationService $notificationService,
        InventoryService $inventoryService,
        PaymentService $paymentService,
        ShippingService $shippingService
    ) {
        $this->notificationService = $notificationService;
        $this->inventoryService = $inventoryService;
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
    }

    /**
     * Handle order created events.
     *
     * @param  \App\Events\OrderCreated $event
     * @return void
     */
    public function handleOrderCreated(OrderCreated $event)
    {
        try {
            // Send order confirmation notification
            $this->notificationService->sendOrderConfirmation($event->order);

            // Update inventory
            $this->inventoryService->updateStockForOrder($event->order);

            // Process payment
            $this->paymentService->processPayment($event->order);

            Log::info('Order created successfully', ['order_id' => $event->order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Error handling order created event',
                [
                'order_id' => $event->order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Handle order status updated events.
     *
     * @param  \App\Events\OrderStatusUpdated $event
     * @return void
     */
    public function handleOrderStatusUpdated(OrderStatusUpdated $event)
    {
        try {
            // Send status update notification
            $this->notificationService->sendOrderStatusUpdate($event->order);

            // Handle specific status updates
            switch ($event->order->status) {
                case 'processing':
                    $this->handleProcessingStatus($event->order);
                    break;
                case 'shipped':
                    $this->handleShippedStatus($event->order);
                    break;
                case 'delivered':
                    $this->handleDeliveredStatus($event->order);
                    break;
                case 'cancelled':
                    $this->handleCancelledStatus($event->order);
                    break;
                case 'refunded':
                    $this->handleRefundedStatus($event->order);
                    break;
            }

            Log::info(
                'Order status updated',
                [
                'order_id' => $event->order->id,
                'status' => $event->order->status
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Error handling order status update',
                [
                'order_id' => $event->order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Handle processing status.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    protected function handleProcessingStatus($order)
    {
        // Additional processing logic
    }

    /**
     * Handle shipped status.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    protected function handleShippedStatus($order)
    {
        $this->shippingService->generateTrackingNumber($order);
        $this->notificationService->sendShippingConfirmation($order);
    }

    /**
     * Handle delivered status.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    protected function handleDeliveredStatus($order)
    {
        $this->notificationService->sendDeliveryConfirmation($order);
    }

    /**
     * Handle cancelled status.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    protected function handleCancelledStatus($order)
    {
        $this->inventoryService->restoreStockForOrder($order);
        $this->paymentService->processRefund($order);
        $this->notificationService->sendOrderCancellation($order);
    }

    /**
     * Handle refunded status.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    protected function handleRefundedStatus($order)
    {
        $this->paymentService->processRefund($order);
        $this->notificationService->sendRefundConfirmation($order);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            OrderCreated::class,
            [OrderEventSubscriber::class, 'handleOrderCreated']
        );

        $events->listen(
            OrderStatusUpdated::class,
            [OrderEventSubscriber::class, 'handleOrderStatusUpdated']
        );
    }
}
