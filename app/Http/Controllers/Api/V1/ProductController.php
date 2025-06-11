<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::with(['category', 'brand']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate($request->get('per_page', 15));

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
     * @OA\Property(property="description",                type="string", example="Product Description")
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sku' => 'required|string|unique:products,sku',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            $product = new Product($request->except('images'));
            $product->slug = Str::slug($request->name);
            $product->save();

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            return $this->respondWithSuccess(
                $product->load(['category', 'brand', 'images']),
                'Product created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to create product', 500, $e->getMessage());
        }
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
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'category_id' => 'exists:categories,id',
            'brand_id' => 'exists:brands,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sku' => 'string|unique:products,sku,' . $product->id,
            'status' => 'in:active,inactive',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->respondWithError('Validation failed', 422, $validator->errors());
        }

        try {
            if ($request->has('name')) {
                $product->slug = Str::slug($request->name);
            }

            $product->update($request->except('images'));

            // Handle image uploads
            if ($request->hasFile('images')) {
                // Delete old images if requested
                if ($request->has('delete_old_images')) {
                    foreach ($product->images as $image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    }
                }

                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            return $this->respondWithSuccess(
                $product->load(['category', 'brand', 'images']),
                'Product updated successfully'
            );
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to update product', 500, $e->getMessage());
        }
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
        try {
            // Delete associated images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }

            $product->delete();

            return $this->respondWithMessage('Product deleted successfully');
        } catch (\Exception $e) {
            return $this->respondWithError('Failed to delete product', 500, $e->getMessage());
        }
    }
}
