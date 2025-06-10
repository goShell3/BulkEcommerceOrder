<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Http\Requests\Api\V1\Discount\StoreDiscountRequest;
use App\Http\Requests\Api\V1\Discount\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiscountController extends Controller
{
    /**
     * Display a listing of the discounts.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $discounts = Discount::latest()->paginate(10);
        return DiscountResource::collection($discounts);
    }

    /**
     * Store a newly created discount in storage.
     *
     * @param StoreDiscountRequest $request
     * @return JsonResponse
     */
    public function store(StoreDiscountRequest $request): JsonResponse
    {
        $discount = Discount::create($request->validated());
        return response()->json([
            'message' => 'Discount created successfully',
            'data' => new DiscountResource($discount)
        ], 201);
    }

    /**
     * Display the specified discount.
     *
     * @param Discount $discount
     * @return DiscountResource
     */
    public function show(Discount $discount): DiscountResource
    {
        return new DiscountResource($discount);
    }

    /**
     * Update the specified discount in storage.
     *
     * @param UpdateDiscountRequest $request
     * @param Discount $discount
     * @return JsonResponse
     */
    public function update(UpdateDiscountRequest $request, Discount $discount): JsonResponse
    {
        $discount->update($request->validated());
        return response()->json([
            'message' => 'Discount updated successfully',
            'data' => new DiscountResource($discount)
        ]);
    }

    /**
     * Remove the specified discount from storage.
     *
     * @param Discount $discount
     * @return JsonResponse
     */
    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();
        return response()->json([
            'message' => 'Discount deleted successfully'
        ]);
    }
} 