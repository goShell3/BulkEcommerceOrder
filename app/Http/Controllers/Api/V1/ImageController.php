<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Image, Product, User, Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        $image->delete();
        return response()->noContent();
    }

    // Upload for any model (polymorphic)
    public function upload(Request $request, string $modelType, int $modelId)
    {
        $request->validate(['image' => 'required|image|max:2048']);

        // Resolve model (e.g., 'products' → Product::class)
        $model = match($modelType) {
            'users'     => User::findOrFail($modelId),
            'products'  => Product::findOrFail($modelId),
            'categories'=> Category::findOrFail($modelId),
            default     => abort(404, 'Invalid model type'),
        };

        // Store image
        $path = $request->file('image')->store($modelType, 'public');

        // Create image record
        $image = $model->images()->create([
            'path' => $path,
            'disk' => 'public',
            'is_primary' => !$model->images()->exists(),
        ]);

        return response()->json([
            'url' => $image->url,
        ], 201);
    }
}
