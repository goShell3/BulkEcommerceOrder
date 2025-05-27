<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="OrderItem",
 *     description="Order item model",
 * @OA\Property(property="id",         type="integer", example=1),
 * @OA\Property(property="order_id",   type="integer", example=1),
 * @OA\Property(property="product_id", type="integer", example=1),
 * @OA\Property(property="quantity",   type="integer", example=2),
 * @OA\Property(property="price",      type="number", format="float", example=49.99),
 * @OA\Property(property="subtotal",   type="number", format="float", example=99.98),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time"),
 * @OA\Property(
 *         property="product",
 *         ref="#/components/schemas/Product"
 *     )
 * )
 */
class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
} 
