<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     type="object",
 *     required={"id", "name", "email"},
 * @OA\Property(property="id",         type="integer", example=1),
 * @OA\Property(property="name",       type="string", example="John Doe"),
 * @OA\Property(property="email",      type="string", format="email", example="user@example.com"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
