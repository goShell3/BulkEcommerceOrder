<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingCarrier;
use Illuminate\Support\Facades\Validator;

class ShippingCarrierController extends Controller
{
    public function index()
    {
        $carriers = ShippingCarrier::all();
        return response()->json(['data' => $carriers]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_carriers',
            'is_active' => 'boolean',
            'tracking_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $carrier = ShippingCarrier::create($request->all());
        return response()->json(['data' => $carrier], 201);
    }

    public function show(ShippingCarrier $shippingCarrier)
    {
        return response()->json(['data' => $shippingCarrier]);
    }

    public function update(Request $request, ShippingCarrier $shippingCarrier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:shipping_carriers,code,' . $shippingCarrier->id,
            'is_active' => 'boolean',
            'tracking_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shippingCarrier->update($request->all());
        return response()->json(['data' => $shippingCarrier]);
    }

    public function destroy(ShippingCarrier $shippingCarrier)
    {
        $shippingCarrier->delete();
        return response()->json(null, 204);
    }
} 