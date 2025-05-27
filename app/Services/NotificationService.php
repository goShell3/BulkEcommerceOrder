<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderConfirmation;
use App\Notifications\OrderStatusUpdate;
use App\Notifications\OrderCancellation;
use App\Notifications\OrderRefundConfirmation;
use App\Notifications\ReturnRequestConfirmation;
use App\Notifications\ReturnRequestStatusUpdate;
use App\Notifications\ReturnRequestRejection;
use App\Notifications\ReturnRequestCompletion;
use App\Notifications\ReturnShippingLabel;
use App\Notifications\ShippingConfirmation;
use App\Notifications\DeliveryConfirmation;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send order confirmation notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendOrderConfirmation(Order $order)
    {
        try {
            $order->user->notify(new OrderConfirmation($order));
            Log::info('Order confirmation sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Failed to send order confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send order status update notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendOrderStatusUpdate(Order $order)
    {
        try {
            $order->user->notify(new OrderStatusUpdate($order));
            Log::info(
                'Order status update sent', [
                'order_id' => $order->id,
                'status' => $order->status
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send order status update', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send order cancellation notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendOrderCancellation(Order $order)
    {
        try {
            $order->user->notify(new OrderCancellation($order));
            Log::info('Order cancellation sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Failed to send order cancellation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send refund confirmation notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendRefundConfirmation(Order $order)
    {
        try {
            $order->user->notify(new OrderRefundConfirmation($order));
            Log::info('Refund confirmation sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Failed to send refund confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send shipping confirmation notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendShippingConfirmation(Order $order)
    {
        try {
            $order->user->notify(new ShippingConfirmation($order));
            Log::info('Shipping confirmation sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Failed to send shipping confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send delivery confirmation notification.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function sendDeliveryConfirmation(Order $order)
    {
        try {
            $order->user->notify(new DeliveryConfirmation($order));
            Log::info('Delivery confirmation sent', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            Log::error(
                'Failed to send delivery confirmation', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send return request confirmation notification.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function sendReturnRequestConfirmation(ReturnRequest $returnRequest)
    {
        try {
            $returnRequest->order->user->notify(new ReturnRequestConfirmation($returnRequest));
            Log::info(
                'Return request confirmation sent', [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send return request confirmation', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send return request status update notification.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function sendReturnRequestStatusUpdate(ReturnRequest $returnRequest)
    {
        try {
            $returnRequest->order->user->notify(new ReturnRequestStatusUpdate($returnRequest));
            Log::info(
                'Return request status update sent', [
                'return_request_id' => $returnRequest->id,
                'status' => $returnRequest->status
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send return request status update', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send return request rejection notification.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function sendReturnRequestRejection(ReturnRequest $returnRequest)
    {
        try {
            $returnRequest->order->user->notify(new ReturnRequestRejection($returnRequest));
            Log::info(
                'Return request rejection sent', [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send return request rejection', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send return request completion notification.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function sendReturnRequestCompletion(ReturnRequest $returnRequest)
    {
        try {
            $returnRequest->order->user->notify(new ReturnRequestCompletion($returnRequest));
            Log::info(
                'Return request completion sent', [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send return request completion', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Send return shipping label notification.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function sendReturnShippingLabel(ReturnRequest $returnRequest)
    {
        try {
            $returnRequest->order->user->notify(new ReturnShippingLabel($returnRequest));
            Log::info(
                'Return shipping label sent', [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to send return shipping label', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }

    /**
     * Notify admin about new return request.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function notifyAdminAboutReturnRequest(ReturnRequest $returnRequest)
    {
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new ReturnRequestConfirmation($returnRequest));
            Log::info(
                'Admin notification sent for return request', [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to notify admin about return request', [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
        }
    }
} 
