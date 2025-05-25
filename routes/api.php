<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ReturnRequestController;
use App\Http\Controllers\Api\V1\QuoteRequestController;
use App\Http\Controllers\Api\V1\B2BOrderController;
use App\Http\Controllers\Api\V1\DiscountController;
use App\Http\Controllers\Api\V1\ShippingCarrierController;
use App\Http\Controllers\Api\V1\ShippingMethodController;
use App\Http\Controllers\Api\V1\PaymentGatewayConfigController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Products and Categories (public)
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{brand}', [BrandController::class, 'show']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::get('user', [UserController::class, 'profile']);
        Route::put('user', [UserController::class, 'update']);
        Route::put('user/password', [UserController::class, 'updatePassword']);

        // Address management
        Route::apiResource('addresses', AddressController::class);

        // Cart management
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{cartItem}', [CartController::class, 'update']);
        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::post('orders/{order}/return', [OrderController::class, 'requestReturn']);

        // Return Requests
        Route::apiResource('return-requests', ReturnRequestController::class);
        Route::post('return-requests/{returnRequest}/cancel', [ReturnRequestController::class, 'cancel']);

        // B2B routes
        Route::middleware('role:b2b')->group(function () {
            Route::get('/b2b/products', [B2BProductController::class, 'index']);
            Route::get('/b2b/orders', [B2BOrderController::class, 'index']);
        });

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            // Product management
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
            
            // Category management
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
            
            // Order management
            Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);
            Route::put('orders/{order}/shipping', [OrderController::class, 'updateShipping']);
            
            // Return request management
            Route::put('return-requests/{returnRequest}/status', [ReturnRequestController::class, 'updateStatus']);
            Route::put('return-requests/{returnRequest}/approve', [ReturnRequestController::class, 'approve']);
            Route::put('return-requests/{returnRequest}/reject', [ReturnRequestController::class, 'reject']);

            // Discount management
            Route::apiResource('discounts', DiscountController::class);

            // B2B management
            Route::apiResource('b2b/users', B2BUserController::class);
            Route::apiResource('b2b/products', B2BProductController::class)->except(['index']);
            Route::apiResource('b2b/orders', B2BOrderController::class)->except(['index']);

            // Shipping management
            Route::apiResource('shipping-carriers', ShippingCarrierController::class);
            Route::apiResource('shipping-methods', ShippingMethodController::class);

            // Payment gateway management
            Route::apiResource('payment-gateways', PaymentGatewayConfigController::class);
        });

        // Authentication routes
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);
    });
});