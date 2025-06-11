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

// Simple test route
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Products and Categories (public)
    Route::get('products', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('api.products.show');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');
    Route::get('brands', [BrandController::class, 'index'])->name('api.brands.index');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->name('api.brands.show');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::get('/user', [UserController::class, 'profile']);
        Route::put('/user', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        // Address management
        Route::apiResource('addresses', AddressController::class)->names([
            'index' => 'api.addresses.index',
            'store' => 'api.addresses.store',
            'show' => 'api.addresses.show',
            'update' => 'api.addresses.update',
            'destroy' => 'api.addresses.destroy',
        ]);

        // Cart management
        Route::get('cart', [CartController::class, 'index'])->name('api.cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('api.cart.store');
        Route::put('cart/{cartItem}', [CartController::class, 'update'])->name('api.cart.update');
        Route::delete('cart/{cartItem}', [CartController::class, 'destroy'])->name('api.cart.destroy');

        // Orders
        Route::apiResource('orders', OrderController::class)->names([
            'index' => 'api.orders.index',
            'store' => 'api.orders.store',
            'show' => 'api.orders.show',
            'update' => 'api.orders.update',
            'destroy' => 'api.orders.destroy',
        ]);
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
        Route::post('orders/{order}/return', [OrderController::class, 'requestReturn'])->name('api.orders.return');

        // Return Requests
        Route::apiResource('return-requests', ReturnRequestController::class)->names([
            'index' => 'api.returns.index',
            'store' => 'api.returns.store',
            'show' => 'api.returns.show',
            'update' => 'api.returns.update',
            'destroy' => 'api.returns.destroy',
        ]);
        Route::post('return-requests/{returnRequest}/cancel', [ReturnRequestController::class, 'cancel'])->name('api.returns.cancel');

        // B2B routes
        Route::middleware('role:b2b')->group(function () {
            Route::get('b2b/products', [B2BProductController::class, 'index'])->name('api.b2b.products.index');
            Route::get('b2b/orders', [B2BOrderController::class, 'index'])->name('api.b2b.orders.index');
        });

        // Shipping management
        Route::get('/shipping-carriers', [ShippingCarrierController::class, 'index']);
        Route::post('/shipping-carriers', [ShippingCarrierController::class, 'store']);
        Route::get('/shipping-carriers/{shippingCarrier}', [ShippingCarrierController::class, 'show']);
        Route::put('/shipping-carriers/{shippingCarrier}', [ShippingCarrierController::class, 'update']);
        Route::delete('/shipping-carriers/{shippingCarrier}', [ShippingCarrierController::class, 'destroy']);

        // Shipping methods
        Route::get('/shipping-methods', [ShippingMethodController::class, 'index']);
        Route::post('/shipping-methods', [ShippingMethodController::class, 'store']);
        Route::get('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'show']);
        Route::put('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'update']);
        Route::delete('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'destroy']);

        // Payment gateways
        Route::get('/payment-gateways', [PaymentGatewayConfigController::class, 'index']);
        Route::post('/payment-gateways', [PaymentGatewayConfigController::class, 'store']);
        Route::get('/payment-gateways/{paymentGatewayConfig}', [PaymentGatewayConfigController::class, 'show']);
        Route::put('/payment-gateways/{paymentGatewayConfig}', [PaymentGatewayConfigController::class, 'update']);
        Route::delete('/payment-gateways/{paymentGatewayConfig}', [PaymentGatewayConfigController::class, 'destroy']);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});