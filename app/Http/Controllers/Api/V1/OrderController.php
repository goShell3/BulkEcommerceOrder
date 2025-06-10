<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Requests\Api\V1\Order\UpdateOrderStatusRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="API Endpoints for order management"
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     summary="Get all orders",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     * @OA\Schema(type="string",     enum={"pending", "processing", "shipped", "delivered", "cancelled"})
     *     ),
     * @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (created_at, total)",
     *         required=false,
     * @OA\Schema(type="string")
     *     ),
     * @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order (asc, desc)",
     *         required=false,
     * @OA\Schema(type="string")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="List of orders",
     * @OA\JsonContent(
     *             type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     * @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = auth()->user()->orders()->with(['items.product', 'shippingAddress', 'billingAddress']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate($request->get('per_page', 15));

        return OrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     summary="Get order details",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Order details",
     * @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show(Order $order): OrderResource
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new OrderResource($order->load(['items.product', 'shippingAddress', 'billingAddress']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"items", "shipping_address"},
     * @OA\Property(
     *                 property="items",
     *                 type="array",
     * @OA\Items(
     *                     type="object",
     *                     required={"product_id", "quantity"},
     * @OA\Property(property="product_id",               type="integer", example=1),
     * @OA\Property(property="quantity",                 type="integer", example=2)
     *                 )
     *             ),
     * @OA\Property(
     *                 property="shipping_address",
     *                 type="object",
     *                 required={"address", "city", "state", "zip_code", "country"},
     * @OA\Property(property="address",                  type="string", example="123 Main St"),
     * @OA\Property(property="city",                     type="string", example="New York"),
     * @OA\Property(property="state",                    type="string", example="NY"),
     * @OA\Property(property="zip_code",                 type="string", example="10001"),
     * @OA\Property(property="country",                  type="string", example="USA")
     *             )
     *         )
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): OrderResource
    {
        $validator = Validator::make($request->all(), [
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string',
            'shipping_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Calculate order total
            $total = 0;
            $items = collect($request->items)->map(function ($item) use (&$total) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            });

            // Create order
            $order = auth()->user()->orders()->create([
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
                'payment_method' => $request->payment_method,
                'shipping_method' => $request->shipping_method,
                'total' => $total,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Create order items
            $order->items()->createMany($items);

            // Update product stock
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return new OrderResource($order->load(['items.product', 'shippingAddress', 'billingAddress']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create order', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/orders/{id}/status",
     *     summary="Update order status",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"status"},
     * @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"processing", "shipped", "delivered", "cancelled"},
     *                 example="processing"
     *             )
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Order status updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateStatus(Request $request, Order $order): OrderResource
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $order->update($request->all());

            // If order is cancelled, restore product stock
            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    $product->increment('stock', $item->quantity);
                }
            }

            return new OrderResource($order->load(['items.product', 'shippingAddress', 'billingAddress']));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update order status', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders/{id}/cancel",
     *     summary="Cancel an order",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     * @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Order cannot be cancelled"
     *     )
     * )
     */
    public function cancel(Order $order): OrderResource
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status === 'cancelled') {
            return response()->json(['message' => 'Order is already cancelled'], 400);
        }

        if ($order->status === 'delivered') {
            return response()->json(['message' => 'Cannot cancel a delivered order'], 400);
        }

        try {
            DB::beginTransaction();

            $order->update(['status' => 'cancelled']);

            // Restore product stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            DB::commit();

            return new OrderResource($order->load(['items.product', 'shippingAddress', 'billingAddress']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to cancel order', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders/{id}/return",
     *     summary="Request order return",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"reason"},
     * @OA\Property(property="reason",                           type="string", example="Product damaged"),
     * @OA\Property(property="description",                      type="string", example="The product arrived with visible damage")
     *         )
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="Return request created successfully",
     * @OA\JsonContent(ref="#/components/schemas/ReturnRequest")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Return request cannot be created"
     *     )
     * )
     */
    public function requestReturn(Request $request, Order $order): JsonResponse
    {
        if (!$order->canBeReturned()) {
            return response()->json(['message' => 'Order cannot be returned'], 422);
        }

        $returnRequest = $order->returnRequests()->create(
            [
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending'
            ]
        );

        return response()->json(new ReturnRequestResource($returnRequest), 201);
    }
} 
