<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Validator;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::with('carrier')->get();
        return response()->json(['data' => $methods]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'carrier_id' => 'required|exists:shipping_carriers,id',
            'code' => 'required|string|max:50|unique:shipping_methods',
            'is_active' => 'boolean',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $method = ShippingMethod::create($request->all());
        return response()->json(['data' => $method], 201);
    }

    public function show(ShippingMethod $shippingMethod)
    {
        return response()->json(['data' => $shippingMethod->load('carrier')]);
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'carrier_id' => 'exists:shipping_carriers,id',
            'code' => 'string|max:50|unique:shipping_methods,code,' . $shippingMethod->id,
            'is_active' => 'boolean',
            'base_price' => 'numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shippingMethod->update($request->all());
        return response()->json(['data' => $shippingMethod->load('carrier')]);
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();
        return response()->json(null, 204);
    }
} 