<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for product management"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Get all products",
     *     tags={"Products"},
     * @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in name and description",
     *         required=false,
     * @OA\Schema(type="string")
     *     ),
     * @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (price, created_at)",
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
     *         description="List of products",
     * @OA\JsonContent(
     *             type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     * @OA\Property(property="meta", type="object", @OA\Property(property="total", type="integer"))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Product::query();

        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        if (request('search')) {
            $query->where(
                function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%')
                        ->orWhere('description', 'like', '%' . request('search') . '%');
                }
            );
        }

        if (request('sort')) {
            $order = request('order', 'asc');
            $query->orderBy(request('sort'), $order);
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Get product details",
     *     tags={"Products"},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Product details",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"name","description","price","category_id"},
     * @OA\Property(property="name",                       type="string", example="Product Name"),
     * @OA\Property(property="description",                type="string", example="Product Description"),
     * @OA\Property(property="price",                      type="number", format="float", example=99.99),
     * @OA\Property(property="category_id",                type="integer", example=1),
     * @OA\Property(property="stock",                      type="integer", example=100)
     *         )
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
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
     * @OA\Property(property="name",                       type="string", example="Updated Product Name"),
     * @OA\Property(property="description",                type="string", example="Updated Description"),
     * @OA\Property(property="price",                      type="number", format="float", example=149.99),
     * @OA\Property(property="category_id",                type="integer", example=1),
     * @OA\Property(property="stock",                      type="integer", example=50)
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
} 
