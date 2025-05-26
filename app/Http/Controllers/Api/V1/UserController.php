<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Get the authenticated user's profile.
     */
    public function profile(): JsonResponse
    {
        $user = $this->userService->getProfile();

        return response()->json(new UserResource($user));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->validated());

        return response()->json(new UserResource($user));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword(
            $request->validated('current_password'),
            $request->validated('password')
        );

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
} 