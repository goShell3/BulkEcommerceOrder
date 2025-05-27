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
    public function index(): AnonymousResourceCollection
    {
        $query = Order::query();

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('sort')) {
            $order = request('order', 'desc');
            $query->orderBy(request('sort'), $order);
        }

        $orders = $query->with(['items.product', 'user'])->paginate(10);
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
        return new OrderResource($order->load(['items.product', 'user']));
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
    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = Order::create(
            [
            'user_id' => auth()->id(),
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'total' => 0, // Will be calculated in the observer
            ]
        );

        foreach ($request->items as $item) {
            $order->items()->create(
                [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => 0, // Will be set from product price
                ]
            );
        }

        return new OrderResource($order->load(['items.product', 'user']));
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
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): OrderResource
    {
        $order->update(['status' => $request->status]);
        return new OrderResource($order->load(['items.product', 'user']));
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
        if (!$order->canBeCancelled()) {
            return response()->json(['message' => 'Order cannot be cancelled'], 422);
        }

        $order->update(['status' => 'cancelled']);
        return new OrderResource($order->load(['items.product', 'user']));
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
