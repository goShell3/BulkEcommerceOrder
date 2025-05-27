<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(): User
    {
        return Auth::user();
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateProfile(array $data): User
    {
        $user = Auth::user();
        $user->update($data);

        return $user->fresh();
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(string $currentPassword, string $newPassword): void
    {
        $user = Auth::user();

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \InvalidArgumentException('Current password is incorrect');
        }

        $user->update(
            [
            'password' => Hash::make($newPassword)
            ]
        );
    }
} 
