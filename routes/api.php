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
        // Auth routes
        Route::post('refresh', [AuthController::class, 'refresh'])->name('api.refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('me', [AuthController::class, 'me'])->name('api.me');

        // User profile
        Route::get('/user', [UserController::class, 'profile']);
        Route::put('/user', [UserController::class, 'update']);
        Route::put('/user/password', [UserController::class, 'updatePassword']);

        // Address management
        Route::apiResource('addresses', AddressController::class);

        // Cart management
        Route::get('cart', [CartController::class, 'index'])->name('api.cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('api.cart.store');
        Route::put('cart/{cartItem}', [CartController::class, 'update'])->name('api.cart.update');
        Route::delete('cart/{cartItem}', [CartController::class, 'destroy'])->name('api.cart.destroy');

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
        Route::post('orders/{order}/return', [OrderController::class, 'requestReturn'])->name('api.orders.return');

        // Return Requests
        Route::apiResource('return-requests', ReturnRequestController::class);
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

            // Shipping management
            // Route::apiResource('shipping-carriers', ShippingCarrierController::class)->names([
            //     'index' => 'api.admin.shipping.carriers.index',
            //     'store' => 'api.admin.shipping.carriers.store',
            //     'show' => 'api.admin.shipping.carriers.show',
            //     'update' => 'api.admin.shipping.carriers.update',
            //     'destroy' => 'api.admin.shipping.carriers.destroy',
            // ]);
            // Route::apiResource('shipping-methods', ShippingMethodController::class)->names([
            //     'index' => 'api.admin.shipping.methods.index',
            //     'store' => 'api.admin.shipping.methods.store',
            //     'show' => 'api.admin.shipping.methods.show',
            //     'update' => 'api.admin.shipping.methods.update',
            //     'destroy' => 'api.admin.shipping.methods.destroy',
            // ]);

            // // Payment gateway management
            // Route::apiResource('payment-gateways', PaymentGatewayConfigController::class)->names([
            //     'index' => 'api.admin.payment.gateways.index',
            //     'store' => 'api.admin.payment.gateways.store',
            //     'show' => 'api.admin.payment.gateways.show',
            //     'update' => 'api.admin.payment.gateways.update',
            //     'destroy' => 'api.admin.payment.gateways.destroy',
            // ]);
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