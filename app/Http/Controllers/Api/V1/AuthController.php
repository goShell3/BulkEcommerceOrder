<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login user and create token",
     *     tags={"Authentication"},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"email","password"},
     * @OA\Property(property="email",    type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Successful login",
     * @OA\JsonContent(
     * @OA\Property(property="token",    type="string", example="1|abcdef123456"),
     * @OA\Property(property="user",     ref="#/components/schemas/User")
     *         )
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                'email' => ['The provided credentials are incorrect.'],
                ]
            );
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(
            [
            'token' => $token,
            'user' => new UserResource($user)
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name",                  type="string", example="John Doe"),
     * @OA\Property(property="email",                 type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password",              type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     * @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="token",                 type="string", example="1|abcdef123456"),
     * @OA\Property(property="user",                  ref="#/components/schemas/User")
     *         )
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create(
            [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            ]
        );

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(
            [
            'token' => $token,
            'user' => new UserResource($user)
            ], 201
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password",
     *     summary="Send password reset link",
     *     tags={"Authentication"},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email'])
            : response()->json(['message' => 'Unable to send reset link'], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     summary="Reset password",
     *     tags={"Authentication"},
     * @OA\RequestBody(
     *         required=true,
     * @OA\JsonContent(
     *             required={"token","email","password","password_confirmation"},
     * @OA\Property(property="token",                 type="string", example="reset-token"),
     * @OA\Property(property="email",                 type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password",              type="string", format="password", example="newpassword123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     * @OA\Response(
     *         response=200,
     *         description="Password reset successfully"
     *     ),
     * @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully'])
            : response()->json(['message' => 'Unable to reset password'], 400);
    }

    /**
     * Logout user (Revoke the token).
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'User logged out successfully.');
    }

    /**
     * Get the authenticated User.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return $this->sendResponse($request->user(), 'User retrieved successfully.');
    }
} 
