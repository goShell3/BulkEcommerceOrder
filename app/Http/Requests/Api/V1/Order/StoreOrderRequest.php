<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.address' => ['required', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.zip_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required for the order.',
            'items.*.product_id.exists' => 'One or more selected products do not exist.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_address.address.required' => 'Address is required.',
            'shipping_address.city.required' => 'City is required.',
            'shipping_address.state.required' => 'State is required.',
            'shipping_address.zip_code.required' => 'ZIP code is required.',
            'shipping_address.country.required' => 'Country is required.',
        ];
    }
} 
