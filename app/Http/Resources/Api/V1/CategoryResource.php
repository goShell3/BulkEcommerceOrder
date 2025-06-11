<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category model",
 * @OA\Property(property="id",                    type="integer", example=1),
 * @OA\Property(property="name",                  type="string", example="Electronics"),
 * @OA\Property(property="description",           type="string", example="Electronic devices and accessories"),
 * @OA\Property(property="parent_id",             type="integer", nullable=true, example=null),
 * @OA\Property(property="status",                type="string", example="active"),
 * @OA\Property(property="created_at",            type="string", format="date-time"),
 * @OA\Property(property="updated_at",            type="string", format="date-time"),
 * @OA\Property(
 *         property="children",
 *         type="array",
 * @OA\Items(ref="#/components/schemas/Category")
 *     )
 * )
 */
class CategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
