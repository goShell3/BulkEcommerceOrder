<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart()->with('product')->get();
        $total = $cart->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'data' => $cart,
            'total' => $total
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $cartItem = auth()->user()->cart()->updateOrCreate(
                ['product_id' => $request->product_id],
                ['quantity' => DB::raw('quantity + ' . $request->quantity)]
            );

            DB::commit();

            return response()->json([
                'message' => 'Product added to cart successfully',
                'data' => $cartItem->load('product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Cart $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check stock availability
        if ($cartItem->product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 422);
        }

        try {
            $cartItem->update(['quantity' => $request->quantity]);
            return response()->json([
                'message' => 'Cart updated successfully',
                'data' => $cartItem->load('product')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Cart $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $cartItem->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 