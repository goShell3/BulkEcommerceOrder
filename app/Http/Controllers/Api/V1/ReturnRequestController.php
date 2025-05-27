<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReturnRequest\StoreReturnRequestRequest;
use App\Http\Requests\Api\V1\ReturnRequest\UpdateReturnRequestStatusRequest;
use App\Http\Resources\Api\V1\ReturnRequestResource;
use App\Models\ReturnRequest;
use App\Models\ReturnItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="ReturnRequests",
 *     description="API Endpoints for return request management"
 * )
 */
class ReturnRequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/return-requests",
     *     summary="Get all return requests",
     *     tags={"ReturnRequests"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by return request status",
     *         required=false,
     * @OA\Schema(type="string",     enum={"pending", "approved", "rejected", "completed"})
     *     ),
     * @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (created_at)",
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
     *         description="List of return requests",
     * @OA\JsonContent(
     *             type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ReturnRequest")),
     * @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer"))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $query = ReturnRequest::query();

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('sort')) {
            $order = request('order', 'desc');
            $query->orderBy(request('sort'), $order);
        }

        $returnRequests = $query->with(['order', 'order.items.product'])->paginate(10);
        return ReturnRequestResource::collection($returnRequests);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/return-requests/{id}",
     *     summary="Get return request details",
     *     tags={"ReturnRequests"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Return request details",
     * @OA\JsonContent(ref="#/components/schemas/ReturnRequest")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Return request not found"
     *     )
     * )
     */
    public function show(ReturnRequest $returnRequest): ReturnRequestResource
    {
        return new ReturnRequestResource($returnRequest->load(['order', 'order.items.product']));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/return-requests",
     *     summary="Create a new return request",
     *     tags={"ReturnRequests"},
     *     security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"order_id", "reason"},
     * @OA\Property(property="order_id",                         type="integer", example=1),
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
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreReturnRequestRequest $request): ReturnRequestResource
    {
        $returnRequest = ReturnRequest::create(
            [
            'order_id' => $request->order_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending'
            ]
        );

        return new ReturnRequestResource($returnRequest->load(['order', 'order.items.product']));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/return-requests/{id}/status",
     *     summary="Update return request status",
     *     tags={"ReturnRequests"},
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
     *                 enum={"approved", "rejected", "completed"},
     *                 example="approved"
     *             )
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Return request status updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/ReturnRequest")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Return request not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateStatus(UpdateReturnRequestStatusRequest $request, ReturnRequest $returnRequest): ReturnRequestResource
    {
        $returnRequest->update(['status' => $request->status]);
        return new ReturnRequestResource($returnRequest->load(['order', 'order.items.product']));
    }

    /**
     * Process refund for approved return request.
     *
     * @param  ReturnRequest $returnRequest
     * @return void
     */
    private function processRefund(ReturnRequest $returnRequest): void
    {
        // Implement refund logic here
        // This could involve calling a payment gateway API
        // and updating the order's payment status
    }

    /**
     * Process replacement for approved return request.
     *
     * @param  ReturnRequest $returnRequest
     * @return void
     */
    private function processReplacement(ReturnRequest $returnRequest): void
    {
        // Implement replacement logic here
        // This could involve creating a new order
        // with the same items
    }
} 
