<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Update stock for an order.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function updateStockForOrder(Order $order)
    {
        try {
            DB::beginTransaction();

            foreach ($order->items as $item) {
                $product = $item->product;
                $newStock = $product->stock - $item->quantity;

                if ($newStock < 0) {
                    throw new \Exception("Insufficient stock for product {$product->id}");
                }

                $product->update(['stock' => $newStock]);
            }

            DB::commit();
            Log::info('Stock updated for order', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Failed to update stock for order',
                [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    /**
     * Restore stock for a cancelled order.
     *
     * @param  \App\Models\Order $order
     * @return void
     */
    public function restoreStockForOrder(Order $order)
    {
        try {
            DB::beginTransaction();

            foreach ($order->items as $item) {
                $product = $item->product;
                $newStock = $product->stock + $item->quantity;

                $product->update(['stock' => $newStock]);
            }

            DB::commit();
            Log::info('Stock restored for cancelled order', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Failed to restore stock for cancelled order',
                [
                'order_id' => $order->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    /**
     * Restore stock for a return request.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return void
     */
    public function restoreStockForReturn(ReturnRequest $returnRequest)
    {
        try {
            DB::beginTransaction();

            foreach ($returnRequest->items as $item) {
                $product = $item->product;
                $newStock = $product->stock + $item->quantity;

                $product->update(['stock' => $newStock]);
            }

            DB::commit();
            Log::info(
                'Stock restored for return request',
                [
                'return_request_id' => $returnRequest->id
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Failed to restore stock for return request',
                [
                'return_request_id' => $returnRequest->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }

    /**
     * Check if product has sufficient stock.
     *
     * @param  \App\Models\Product $product
     * @param  int                 $quantity
     * @return bool
     */
    public function hasSufficientStock(Product $product, int $quantity): bool
    {
        return $product->stock >= $quantity;
    }

    /**
     * Get available stock for a product.
     *
     * @param  \App\Models\Product $product
     * @return int
     */
    public function getAvailableStock(Product $product): int
    {
        return $product->stock;
    }

    /**
     * Update product stock.
     *
     * @param  \App\Models\Product $product
     * @param  int                 $quantity
     * @return void
     */
    public function updateProductStock(Product $product, int $quantity)
    {
        try {
            $product->update(['stock' => $quantity]);
            Log::info(
                'Product stock updated',
                [
                'product_id' => $product->id,
                'new_stock' => $quantity
                ]
            );
        } catch (\Exception $e) {
            Log::error(
                'Failed to update product stock',
                [
                'product_id' => $product->id,
                'error' => $e->getMessage()
                ]
            );
            throw $e;
        }
    }
}
