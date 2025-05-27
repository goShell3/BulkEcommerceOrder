<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 * @OA\Property(property="id",          type="integer", example=1),
 * @OA\Property(property="name",        type="string", example="Product Name"),
 * @OA\Property(property="description", type="string", example="Product Description"),
 * @OA\Property(property="price",       type="number", format="float", example=99.99),
 * @OA\Property(property="category_id", type="integer", example=1),
 * @OA\Property(property="stock",       type="integer", example=100),
 * @OA\Property(property="status",      type="string", example="active"),
 * @OA\Property(property="created_at",  type="string", format="date-time"),
 * @OA\Property(property="updated_at",  type="string", format="date-time"),
 * @OA\Property(
 *         property="category",
 *         ref="#/components/schemas/Category"
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'stock' => $this->stock,
            'status' => $this->status,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
} 
