<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\ShippingCarrier;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Generate tracking number for an order.
     *
     * @param  \App\Models\Order  $order
     * @return string
     */
    public function generateTrackingNumber(Order $order): string
    {
        try {
            $carrier = ShippingCarrier::find($order->shipping_carrier_id);
            if (!$carrier) {
                throw new \Exception('Shipping carrier not found');
            }

            // Generate tracking number based on carrier format
            $trackingNumber = $this->formatTrackingNumber($carrier, $order);

            // Update order with tracking number
            $order->update(['tracking_number' => $trackingNumber]);

            Log::info('Tracking number generated', [
                'order_id' => $order->id,
                'tracking_number' => $trackingNumber
            ]);

            return $trackingNumber;
        } catch (\Exception $e) {
            Log::error('Failed to generate tracking number', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate shipping cost for an order.
     *
     * @param  \App\Models\Order  $order
     * @return float
     */
    public function calculateShippingCost(Order $order): float
    {
        try {
            $shippingMethod = ShippingMethod::find($order->shipping_method_id);
            if (!$shippingMethod) {
                throw new \Exception('Shipping method not found');
            }

            $baseCost = $shippingMethod->base_cost;
            $weightCost = $this->calculateWeightCost($order, $shippingMethod);
            $distanceCost = $this->calculateDistanceCost($order, $shippingMethod);

            $totalCost = $baseCost + $weightCost + $distanceCost;

            Log::info('Shipping cost calculated', [
                'order_id' => $order->id,
                'shipping_cost' => $totalCost
            ]);

            return $totalCost;
        } catch (\Exception $e) {
            Log::error('Failed to calculate shipping cost', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate weight-based shipping cost.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\ShippingMethod  $shippingMethod
     * @return float
     */
    protected function calculateWeightCost(Order $order, ShippingMethod $shippingMethod): float
    {
        $totalWeight = 0;
        foreach ($order->items as $item) {
            $totalWeight += $item->product->weight * $item->quantity;
        }

        return $totalWeight * $shippingMethod->weight_rate;
    }

    /**
     * Calculate distance-based shipping cost.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\ShippingMethod  $shippingMethod
     * @return float
     */
    protected function calculateDistanceCost(Order $order, ShippingMethod $shippingMethod): float
    {
        // Implement distance calculation logic here
        // This could involve using a geocoding service to calculate distance
        // between warehouse and delivery address
        return 0;
    }

    /**
     * Format tracking number based on carrier format.
     *
     * @param  \App\Models\ShippingCarrier  $carrier
     * @param  \App\Models\Order  $order
     * @return string
     */
    protected function formatTrackingNumber(ShippingCarrier $carrier, Order $order): string
    {
        $prefix = $carrier->tracking_prefix;
        $timestamp = time();
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Get available shipping methods for an order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableShippingMethods(Order $order)
    {
        return ShippingMethod::where('is_active', true)
            ->where('min_order_amount', '<=', $order->subtotal)
            ->get();
    }

    /**
     * Get shipping carrier by ID.
     *
     * @param  int  $carrierId
     * @return \App\Models\ShippingCarrier|null
     */
    public function getShippingCarrier(int $carrierId)
    {
        return ShippingCarrier::find($carrierId);
    }
} 