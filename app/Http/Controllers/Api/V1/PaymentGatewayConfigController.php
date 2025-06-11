<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGatewayConfig;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayConfigController extends Controller
{
    public function index()
    {
        $gateways = PaymentGatewayConfig::all();
        return response()->json(['data' => $gateways]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_gateway_configs',
            'is_active' => 'boolean',
            'credentials' => 'required|array',
            'test_mode' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['config'] = $data['credentials'];
        unset($data['credentials']);

        $gateway = PaymentGatewayConfig::create($data);
        return response()->json(['data' => $gateway], 201);
    }

    public function show(PaymentGatewayConfig $paymentGatewayConfig)
    {
        return response()->json(['data' => $paymentGatewayConfig]);
    }

    public function update(Request $request, PaymentGatewayConfig $paymentGatewayConfig)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'code' => 'string|max:50|unique:payment_gateway_configs,code,' . $paymentGatewayConfig->id,
            'is_active' => 'boolean',
            'credentials' => 'array',
            'test_mode' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (isset($data['credentials'])) {
            $data['config'] = $data['credentials'];
            unset($data['credentials']);
        }

        $paymentGatewayConfig->update($data);
        return response()->json(['data' => $paymentGatewayConfig]);
    }

    public function destroy(PaymentGatewayConfig $paymentGatewayConfig)
    {
        $paymentGatewayConfig->delete();
        return response()->json(null, 204);
    }
} 