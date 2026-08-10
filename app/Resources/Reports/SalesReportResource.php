<?php

namespace App\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'summary' => $this->when(isset($this->resource['summary']), $this->resource['summary'] ?? null),
            'period' => $this->when(isset($this->resource['period']), $this->resource['period'] ?? null),
            'date_from' => $this->when(isset($this->resource['date_from']), $this->resource['date_from'] ?? null),
            'date_to' => $this->when(isset($this->resource['date_to']), $this->resource['date_to'] ?? null),
            'total_orders' => $this->when(isset($this->resource['total_orders']), $this->resource['total_orders'] ?? 0),
            'total_revenue' => $this->when(isset($this->resource['total_revenue']), (float) ($this->resource['total_revenue'] ?? 0)),
            'total_items_sold' => $this->when(isset($this->resource['total_items_sold']), $this->resource['total_items_sold'] ?? 0),
            'average_order_value' => $this->when(isset($this->resource['average_order_value']), (float) ($this->resource['average_order_value'] ?? 0)),
            'top_products' => $this->when(isset($this->resource['top_products']), $this->resource['top_products'] ?? []),
            'top_customers' => $this->when(isset($this->resource['top_customers']), $this->resource['top_customers'] ?? []),
            'daily_breakdown' => $this->when(isset($this->resource['daily_breakdown']), $this->resource['daily_breakdown'] ?? []),
            'category_breakdown' => $this->when(isset($this->resource['category_breakdown']), $this->resource['category_breakdown'] ?? []),
            'payment_method_breakdown' => $this->when(isset($this->resource['payment_method_breakdown']), $this->resource['payment_method_breakdown'] ?? []),
        ];
    }
}
