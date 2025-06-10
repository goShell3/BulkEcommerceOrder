<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\Api\V1\B2BOrder\StoreB2BOrderRequest;
use App\Http\Requests\Api\V1\B2BOrder\UpdateB2BOrderRequest;
use App\Http\Resources\B2BOrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class B2BOrderController extends Controller
{
    /**
     * Display a listing of B2B orders.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::where('is_b2b', true)
            ->with(['user', 'items', 'shipping', 'billing'])
            ->latest()
            ->paginate(10);
        return B2BOrderResource::collection($orders);
    }

    /**
     * Store a newly created B2B order in storage.
     *
     * @param StoreB2BOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreB2BOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                ...$request->validated(),
                'is_b2b' => true,
                'status' => 'pending_approval',
            ]);

            // Create order items
            foreach ($request->items as $item) {
                $order->items()->create($item);
            }

            // Create shipping address
            if ($request->has('shipping_address')) {
                $order->shipping()->create($request->shipping_address);
            }

            // Create billing address
            if ($request->has('billing_address')) {
                $order->billing()->create($request->billing_address);
            }

            DB::commit();

            return response()->json([
                'message' => 'B2B order created successfully',
                'data' => new B2BOrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create B2B order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified B2B order.
     *
     * @param Order $order
     * @return B2BOrderResource
     */
    public function show(Order $order): B2BOrderResource
    {
        $order->load(['user', 'items', 'shipping', 'billing', 'payments']);
        return new B2BOrderResource($order);
    }

    /**
     * Update the specified B2B order in storage.
     *
     * @param UpdateB2BOrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(UpdateB2BOrderRequest $request, Order $order): JsonResponse
    {
        try {
            DB::beginTransaction();

            $order->update($request->validated());

            if ($request->has('items')) {
                $order->items()->delete();
                foreach ($request->items as $item) {
                    $order->items()->create($item);
                }
            }

            if ($request->has('shipping_address')) {
                $order->shipping()->update($request->shipping_address);
            }

            if ($request->has('billing_address')) {
                $order->billing()->update($request->billing_address);
            }

            DB::commit();

            return response()->json([
                'message' => 'B2B order updated successfully',
                'data' => new B2BOrderResource($order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update B2B order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified B2B order from storage.
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json([
            'message' => 'B2B order deleted successfully'
        ]);
    }

    /**
     * Approve a B2B order.
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function approve(Order $order): JsonResponse
    {
        if ($order->status !== 'pending_approval') {
            return response()->json([
                'message' => 'Order is not pending approval'
            ], 400);
        }

        $order->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);

        return response()->json([
            'message' => 'B2B order approved successfully',
            'data' => new B2BOrderResource($order)
        ]);
    }

    /**
     * Reject a B2B order.
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function reject(Order $order): JsonResponse
    {
        if ($order->status !== 'pending_approval') {
            return response()->json([
                'message' => 'Order is not pending approval'
            ], 400);
        }

        $order->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id()
        ]);

        return response()->json([
            'message' => 'B2B order rejected successfully',
            'data' => new B2BOrderResource($order)
        ]);
    }

    /**
     * Update the status of a B2B order.
     *
     * @param UpdateB2BOrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function updateStatus(UpdateB2BOrderRequest $request, Order $order): JsonResponse
    {
        $order->update([
            'status' => $request->status,
            'status_notes' => $request->status_notes
        ]);

        return response()->json([
            'message' => 'B2B order status updated successfully',
            'data' => new B2BOrderResource($order)
        ]);
    }
} 