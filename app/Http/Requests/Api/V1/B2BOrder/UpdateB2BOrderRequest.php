<?php

namespace App\Http\Requests\Api\V1\B2BOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateB2BOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:pending_approval,approved,rejected,processing,shipped,delivered,cancelled',
            'status_notes' => 'nullable|string|max:255',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
            'shipping_address' => 'sometimes|array',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'billing_address' => 'sometimes|array',
            'billing_address.street' => 'required|string|max:255',
            'billing_address.city' => 'required|string|max:100',
            'billing_address.state' => 'required|string|max:100',
            'billing_address.postal_code' => 'required|string|max:20',
            'billing_address.country' => 'required|string|max:100',
            'shipping_method' => 'sometimes|string|max:50',
            'shipping_cost' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|string|max:50',
            'payment_status' => 'sometimes|in:pending,partial,paid',
            'notes' => 'nullable|string',
            'expected_delivery_date' => 'nullable|date|after:today',
            'purchase_order_number' => 'nullable|string|max:50',
            'tracking_number' => 'nullable|string|max:50',
            'tracking_url' => 'nullable|url|max:255',
        ];
    }
} 