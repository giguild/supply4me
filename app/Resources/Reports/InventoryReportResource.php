<?php

namespace App\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'summary' => $this->when(isset($this->resource['summary']), $this->resource['summary'] ?? null),
            'warehouse' => $this->when(isset($this->resource['warehouse']), $this->resource['warehouse'] ?? null),
            'date' => $this->when(isset($this->resource['date']), $this->resource['date'] ?? null),
            'total_products' => $this->when(isset($this->resource['total_products']), $this->resource['total_products'] ?? 0),
            'total_stock_value' => $this->when(isset($this->resource['total_stock_value']), (float) ($this->resource['total_stock_value'] ?? 0)),
            'total_quantity' => $this->when(isset($this->resource['total_quantity']), $this->resource['total_quantity'] ?? 0),
            'low_stock_count' => $this->when(isset($this->resource['low_stock_count']), $this->resource['low_stock_count'] ?? 0),
            'out_of_stock_count' => $this->when(isset($this->resource['out_of_stock_count']), $this->resource['out_of_stock_count'] ?? 0),
            'overstocked_count' => $this->when(isset($this->resource['overstocked_count']), $this->resource['overstocked_count'] ?? 0),
            'low_stock_products' => $this->when(isset($this->resource['low_stock_products']), $this->resource['low_stock_products'] ?? []),
            'out_of_stock_products' => $this->when(isset($this->resource['out_of_stock_products']), $this->resource['out_of_stock_products'] ?? []),
            'category_breakdown' => $this->when(isset($this->resource['category_breakdown']), $this->resource['category_breakdown'] ?? []),
            'warehouse_breakdown' => $this->when(isset($this->resource['warehouse_breakdown']), $this->resource['warehouse_breakdown'] ?? []),
            'movement_summary' => $this->when(isset($this->resource['movement_summary']), $this->resource['movement_summary'] ?? []),
        ];
    }
}
