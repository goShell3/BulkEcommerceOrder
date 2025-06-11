<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\Api\V1\B2BProduct\StoreB2BProductRequest;
use App\Http\Requests\Api\V1\B2BProduct\UpdateB2BProductRequest;
use App\Http\Resources\B2BProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class B2BProductController extends Controller
{
    /**
     * Display a listing of B2B products.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $products = Product::where('is_b2b', true)
            ->with(['category', 'brand', 'variants', 'stock'])
            ->paginate(10);
        return B2BProductResource::collection($products);
    }

    /**
     * Store a newly created B2B product in storage.
     *
     * @param StoreB2BProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreB2BProductRequest $request): JsonResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'is_b2b' => true,
        ]);

        if ($request->has('variants')) {
            $product->variants()->createMany($request->variants);
        }

        if ($request->has('stock')) {
            $product->stock()->create($request->stock);
        }

        return response()->json([
            'message' => 'B2B product created successfully',
            'data' => new B2BProductResource($product)
        ], 201);
    }

    /**
     * Display the specified B2B product.
     *
     * @param Product $product
     * @return B2BProductResource
     */
    public function show(Product $product): B2BProductResource
    {
        $product->load(['category', 'brand', 'variants', 'stock']);
        return new B2BProductResource($product);
    }

    /**
     * Update the specified B2B product in storage.
     *
     * @param UpdateB2BProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(UpdateB2BProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        if ($request->has('variants')) {
            $product->variants()->delete();
            $product->variants()->createMany($request->variants);
        }

        if ($request->has('stock')) {
            $product->stock()->update($request->stock);
        }

        return response()->json([
            'message' => 'B2B product updated successfully',
            'data' => new B2BProductResource($product)
        ]);
    }

    /**
     * Remove the specified B2B product from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json([
            'message' => 'B2B product deleted successfully'
        ]);
    }

    /**
     * Update the stock of a B2B product.
     *
     * @param UpdateB2BProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function updateStock(UpdateB2BProductRequest $request, Product $product): JsonResponse
    {
        $product->stock()->update($request->only(['quantity', 'min_order_quantity', 'max_order_quantity']));
        return response()->json([
            'message' => 'B2B product stock updated successfully',
            'data' => new B2BProductResource($product)
        ]);
    }

    /**
     * Update the pricing of a B2B product.
     *
     * @param UpdateB2BProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function updatePricing(UpdateB2BProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->only(['b2b_price', 'min_order_quantity', 'bulk_pricing']));
        return response()->json([
            'message' => 'B2B product pricing updated successfully',
            'data' => new B2BProductResource($product)
        ]);
    }
} 