<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'required|exists:product_units,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'type' => 'sometimes|string|in:simple,variant,service',
            'status' => 'sometimes|string|in:active,inactive',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'is_taxable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.sku' => 'required|string|max:100|unique:product_variants,sku',
            'variants.*.cost_price' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
            'variants.*.attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'sku.required' => 'SKU is required',
            'sku.unique' => 'This SKU already exists',
            'category_id.required' => 'Product category is required',
            'unit_id.required' => 'Product unit is required',
            'cost_price.required' => 'Cost price is required',
            'selling_price.required' => 'Selling price is required',
            'selling_price.gte' => 'Selling price must be greater than or equal to cost price',
        ];
    }
}
