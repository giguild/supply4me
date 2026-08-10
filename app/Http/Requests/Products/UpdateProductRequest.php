<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'category_id' => 'sometimes|exists:product_categories,id',
            'brand_id' => 'nullable|exists:product_brands,id',
            'unit_id' => 'sometimes|exists:product_units,id',
            'cost_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'type' => 'sometimes|string|in:simple,variant,service',
            'status' => 'sometimes|string|in:active,inactive',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'minimum_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'is_taxable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU already exists',
            'category_id.exists' => 'Selected category does not exist',
            'unit_id.exists' => 'Selected unit does not exist',
        ];
    }
}
