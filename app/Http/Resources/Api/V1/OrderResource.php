<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Order",
 *     title="Order",
 *     description="Order model",
 * @OA\Property(property="id",                     type="integer", example=1),
 * @OA\Property(property="user_id",                type="integer", example=1),
 * @OA\Property(property="status",                 type="string", example="pending"),
 * @OA\Property(property="total",                  type="number", format="float", example=99.99),
 * @OA\Property(property="shipping_address",       type="object"),
 * @OA\Property(property="created_at",             type="string", format="date-time"),
 * @OA\Property(property="updated_at",             type="string", format="date-time"),
 * @OA\Property(
 *         property="items",
 *         type="array",
 * @OA\Items(ref="#/components/schemas/OrderItem")
 *     ),
 * @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User"
 *     )
 * )
 */
class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total' => $this->total,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
} 
