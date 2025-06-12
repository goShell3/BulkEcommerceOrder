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
use App\Http\Controllers\Api\V1\B2BUserController;
use App\Http\Controllers\Api\V1\B2BProductController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\V1\ImageController;

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
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->withoutMiddleware(['web']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->withoutMiddleware(['web']);

    // Products and Categories (public)
    Route::get('products', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('api.products.show');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');
    Route::get('brands', [BrandController::class, 'index'])->name('api.brands.index');
    Route::get('brands/{brand}', [BrandController::class, 'show'])->name('api.brands.show');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');

        // User routes
        Route::apiResource('users', UserController::class);
        Route::apiResource('addresses', AddressController::class);
        Route::apiResource('cart', CartController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('return-requests', ReturnRequestController::class);
        Route::apiResource('quote-requests', QuoteRequestController::class);
        Route::apiResource('b2b-orders', B2BOrderController::class);
        Route::apiResource('b2b-users', B2BUserController::class);
        Route::apiResource('b2b-products', B2BProductController::class);
        Route::apiResource('discounts', DiscountController::class);
        Route::apiResource('shipping-carriers', ShippingCarrierController::class);
        Route::apiResource('shipping-methods', ShippingMethodController::class);
        Route::apiResource('payment-gateway-configs', PaymentGatewayConfigController::class);
        Route::apiResource('images', ImageController::class);

        // User profile
        Route::get('/user', [UserController::class, 'profile']);
        Route::put('/user', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        // Orders
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
        Route::post('orders/{order}/return', [OrderController::class, 'requestReturn'])->name('api.orders.return');

        // Return Requests
        Route::post('return-requests/{returnRequest}/cancel', [ReturnRequestController::class, 'cancel'])->name('api.returns.cancel');

        // B2B routes
        Route::middleware('role:b2b')->group(function () {
            Route::get('b2b/products', [B2BProductController::class, 'index'])->name('api.b2b.products.index');
            Route::get('b2b/orders', [B2BOrderController::class, 'index'])->name('api.b2b.orders.index');
        });

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            
            // Product management
            Route::apiResource('products', ProductController::class)->except(['index', 'show'])->names([
                'store' => 'api.admin.products.store',
                'update' => 'api.admin.products.update',
                'destroy' => 'api.admin.products.destroy',
            ]);
            
            // Category management 
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show'])->names([
                'store' => 'api.admin.categories.store',
                'update' => 'api.admin.categories.update',
                'destroy' => 'api.admin.categories.destroy',
            ]);
            
            // Order management
            Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('api.admin.orders.status');
            Route::put('orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('api.admin.orders.shipping');
            
            // Return request management
            Route::put('return-requests/{returnRequest}/status', [ReturnRequestController::class, 'updateStatus'])->name('api.admin.returns.status');
            Route::put('return-requests/{returnRequest}/approve', [ReturnRequestController::class, 'approve'])->name('api.admin.returns.approve');
            Route::put('return-requests/{returnRequest}/reject', [ReturnRequestController::class, 'reject'])->name('api.admin.returns.reject');

            // Discount management
            Route::apiResource('discounts', DiscountController::class)->names([
                'index' => 'api.admin.discounts.index',
                'store' => 'api.admin.discounts.store',
                'show' => 'api.admin.discounts.show',
                'update' => 'api.admin.discounts.update',
                'destroy' => 'api.admin.discounts.destroy',
            ]);

            // B2B management
            Route::apiResource('b2b/users', B2BUserController::class)->names([
                'index' => 'api.admin.b2b.users.index',
                'store' => 'api.admin.b2b.users.store',
                'show' => 'api.admin.b2b.users.show',
                'update' => 'api.admin.b2b.users.update',
                'destroy' => 'api.admin.b2b.users.destroy',
            ]);
            Route::apiResource('b2b/products', B2BProductController::class)->except(['index'])->names([
                'store' => 'api.admin.b2b.products.store',
                'show' => 'api.admin.b2b.products.show',
                'update' => 'api.admin.b2b.products.update',
                'destroy' => 'api.admin.b2b.products.destroy',
            ]);
            Route::apiResource('b2b/orders', B2BOrderController::class)->except(['index'])->names([
                'store' => 'api.admin.b2b.orders.store',
                'show' => 'api.admin.b2b.orders.show',
                'update' => 'api.admin.b2b.orders.update',
                'destroy' => 'api.admin.b2b.orders.destroy',
            ]);
        });
    });
});