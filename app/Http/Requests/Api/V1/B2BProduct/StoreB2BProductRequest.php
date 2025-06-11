<?php

namespace App\Http\Requests\Api\V1\B2BProduct;

use Illuminate\Foundation\Http\FormRequest;

class StoreB2BProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'sku' => 'required|string|unique:products,sku',
            'b2b_price' => 'required|numeric|min:0',
            'min_order_quantity' => 'required|integer|min:1',
            'max_order_quantity' => 'nullable|integer|min:1|gt:min_order_quantity',
            'bulk_pricing' => 'nullable|array',
            'bulk_pricing.*.quantity' => 'required|integer|min:1',
            'bulk_pricing.*.price' => 'required|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.sku' => 'required|string|unique:product_variants,sku',
            'variants.*.price' => 'required|numeric|min:0',
            'stock' => 'required|array',
            'stock.quantity' => 'required|integer|min:0',
            'stock.min_order_quantity' => 'required|integer|min:1',
            'stock.max_order_quantity' => 'nullable|integer|min:1|gt:stock.min_order_quantity',
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required|string|max:255',
            'specifications.*.value' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'required|image|max:2048',
            'is_active' => 'boolean',
        ];
    }
} 