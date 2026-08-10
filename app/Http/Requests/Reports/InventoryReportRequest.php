<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class InventoryReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'date' => 'nullable|date',
            'category_id' => 'nullable|exists:product_categories,id',
            'low_stock_only' => 'sometimes|boolean',
            'out_of_stock_only' => 'sometimes|boolean',
        ];
    }
}
