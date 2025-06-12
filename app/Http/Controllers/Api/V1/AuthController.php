<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\RefreshTokenRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info('Login attempt', ['email' => $request->email]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::warning('Login failed: User not found', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'email' => ['No account found with this email address.']
                    ]
                ], 422);
            }

            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Login failed: Invalid password', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'password' => ['The provided password is incorrect.']
                    ]
                ], 422);
            }

            // Revoke existing tokens
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken($request->device_name ?? 'api-token')->plainTextToken;

            Log::info('Login successful', ['email' => $request->email, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($request->device_name ?? 'api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user)
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration',
                'error' => $e->getMessage()
            ], 500);
        }
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
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     summary="Refresh token",
     *     tags={"Authentication"},
     * @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     * @OA\JsonContent(
     * @OA\Property(property="token",    type="string", example="1|abcdef123456"),
     * @OA\Property(property="refresh_token",    type="string", example="2|abcdef123456"),
     * @OA\Property(property="token_type",    type="string", example="Bearer"),
     * @OA\Property(property="expires_in",    type="integer", example="86400")
     *         )
     *     ),
     * @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke the refresh token
        $request->user()->currentAccessToken()->delete();
        
        // Create new tokens
        $token = $user->createToken('api-token', ['*'], now()->addHours(24))->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 86400
        ]);
    }

    /**
     * Logout user (Revoke the token).
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new UserResource($request->user())
            ]);
        } catch (\Exception $e) {
            Log::error('Get user details error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
