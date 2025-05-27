<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class BrandController extends BaseController
{
    /**
     * Display a listing of the brands.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $brands = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($brands, 'Brands retrieved successfully.');
    }

    /**
     * Display the specified brand.
     *
     * @param  Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        $brand->load('products');

        return $this->sendResponse($brand, 'Brand retrieved successfully.');
    }

    /**
     * Store a newly created brand.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'status' => 'required|in:active,inactive',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $brand = Brand::create($request->all());

        return $this->sendResponse($brand, 'Brand created successfully.');
    }

    /**
     * Update the specified brand.
     *
     * @param  Request $request
     * @param  Brand   $brand
     * @return JsonResponse
     */
    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validator = Validator::make(
            $request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'status' => 'sometimes|required|in:active,inactive',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $brand->update($request->all());

        return $this->sendResponse($brand, 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand.
     *
     * @param  Brand $brand
     * @return JsonResponse
     */
    public function destroy(Brand $brand): JsonResponse
    {
        // Check if brand has products
        if ($brand->products()->exists()) {
            return $this->sendError('Cannot delete brand with associated products.', [], 422);
        }

        $brand->delete();

        return $this->sendResponse(null, 'Brand deleted successfully.');
    }
} 
