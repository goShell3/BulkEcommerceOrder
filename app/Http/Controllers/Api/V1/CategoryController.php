<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Category\StoreCategoryRequest;
use App\Http\Requests\Api\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for category management"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     * @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="Filter by parent category ID",
     *         required=false,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="List of categories",
     * @OA\JsonContent(
     *             type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Category::query();

        if (request('parent_id')) {
            $query->where('parent_id', request('parent_id'));
        }

        $categories = $query->with('children')->get();
        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     summary="Get category details",
     *     tags={"Categories"},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Category details",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->load('children'));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"name"},
     * @OA\Property(property="name",                        type="string", example="Electronics"),
     * @OA\Property(property="description",                 type="string", example="Electronic devices and accessories"),
     * @OA\Property(property="parent_id",                   type="integer", example=1),
     * @OA\Property(property="status",                      type="string", example="active")
     *         )
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = Category::create($request->validated());
        return new CategoryResource($category);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
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
     * @OA\Property(property="name",                        type="string", example="Updated Category"),
     * @OA\Property(property="description",                 type="string", example="Updated description"),
     * @OA\Property(property="parent_id",                   type="integer", example=1),
     * @OA\Property(property="status",                      type="string", example="active")
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     * @OA\Schema(type="integer")
     *     ),
     * @OA\Response(
     *         response=204,
     *         description="Category deleted successfully"
     *     ),
     * @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(null, 204);
    }
} 
