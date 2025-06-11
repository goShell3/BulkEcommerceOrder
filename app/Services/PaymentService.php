<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class PaymentService
{
    /**
     * Process payment for an order.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function processPayment(Order $order)
    {
        try {
            DB::beginTransaction();

            // Get payment gateway configuration
            $gatewayConfig = PaymentGatewayConfig::where('is_active', true)->first();
            if (!$gatewayConfig) {
                throw new \Exception('No active payment gateway found');
            }

            // Configure Stripe
            Stripe::setApiKey($gatewayConfig->secret_key);

            // Create payment intent
            $paymentIntent = PaymentIntent::create(
                [
                'amount' => $order->total * 100, // Convert to cents
                'currency' => 'usd',
                'payment_method' => $order->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'order_id' => $order->id
                ]
                ]
            );

            // Create payment record
            Payment::create(
                [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total,
                'currency' => 'usd',
                'status' => $paymentIntent->status,
                'payment_method' => $order->payment_method
                ]
            );

            DB::commit();
            Log::info(
                'Payment processed successfully',
                [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Failed to process payment',
                [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    /**
     * Process refund for an order.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function processRefund(Order $order)
    {
        try {
            DB::beginTransaction();

            // Get payment gateway configuration
            $gatewayConfig = PaymentGatewayConfig::where('is_active', true)->first();
            if (!$gatewayConfig) {
                throw new \Exception('No active payment gateway found');
            }

            // Configure Stripe
            Stripe::setApiKey($gatewayConfig->secret_key);

            // Get payment record
            $payment = Payment::where('order_id', $order->id)->first();
            if (!$payment) {
                throw new \Exception('No payment found for order');
            }

            // Process refund
            $refund = Refund::create(
                [
                'payment_intent' => $payment->payment_intent_id,
                'amount' => $order->total * 100 // Convert to cents
                ]
            );

            // Update payment status
            $payment->update(
                [
                'status' => 'refunded',
                'refund_id' => $refund->id
                ]
            );

            DB::commit();
            Log::info(
                'Refund processed successfully',
                [
                'order_id' => $order->id,
                'refund_id' => $refund->id
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Failed to process refund',
                [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    /**
     * Get payment status.
     *
     * @param  \App\Models\Order $order
     * @return string
     */
    public function getPaymentStatus(Order $order): string
    {
        $payment = Payment::where('order_id', $order->id)->first();
        return $payment ? $payment->status : 'pending';
    }

    /**
     * Verify payment.
     *
     * @param  \App\Models\Order $order
     * @return bool
     */
    public function verifyPayment(Order $order): bool
    {
        try {
            $payment = Payment::where('order_id', $order->id)->first();
            if (!$payment) {
                return false;
            }

            // Get payment gateway configuration
            $gatewayConfig = PaymentGatewayConfig::where('is_active', true)->first();
            if (!$gatewayConfig) {
                throw new \Exception('No active payment gateway found');
            }

            // Configure Stripe
            Stripe::setApiKey($gatewayConfig->secret_key);

            // Retrieve payment intent
            $paymentIntent = PaymentIntent::retrieve($payment->payment_intent_id);

            return $paymentIntent->status === 'succeeded';
        } catch (\Exception $e) {
            Log::error(
                'Failed to verify payment',
                [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
            return false;
        }
    }
}
