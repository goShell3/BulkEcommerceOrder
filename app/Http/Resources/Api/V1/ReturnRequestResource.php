<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ReturnRequest",
 *     title="ReturnRequest",
 *     description="Return request model",
 * @OA\Property(property="id",          type="integer", example=1),
 * @OA\Property(property="order_id",    type="integer", example=1),
 * @OA\Property(property="reason",      type="string", example="Product damaged"),
 * @OA\Property(property="description", type="string", example="The product arrived with visible damage"),
 * @OA\Property(property="status",      type="string", example="pending"),
 * @OA\Property(property="created_at",  type="string", format="date-time"),
 * @OA\Property(property="updated_at",  type="string", format="date-time"),
 * @OA\Property(
 *         property="order",
 *         ref="#/components/schemas/Order"
 *     )
 * )
 */
class ReturnRequestResource extends JsonResource
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
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
} 
