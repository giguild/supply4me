<?php

namespace App\Services\Reporting;

use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * Get a sales summary for a date range.
     */
    public function getSalesSummary(string $startDate, string $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->get();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_subtotal' => $orders->sum('subtotal'),
            'total_tax' => $orders->sum('tax_amount'),
            'total_discount' => $orders->sum('discount_amount'),
            'total_shipping' => $orders->sum('shipping_amount'),
            'average_order_value' => $orders->count() > 0
                ? $orders->sum('total_amount') / $orders->count()
                : 0,
        ];
    }

    /**
     * Get sales broken down by customer.
     */
    public function getSalesByCustomer(string $startDate, string $endDate): Collection
    {
        return Order::select('customer_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_amount'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->groupBy('customer_id')
            ->with('customer')
            ->orderByDesc('total_amount')
            ->get();
    }

    /**
     * Get sales broken down by product.
     */
    public function getSalesByProduct(string $startDate, string $endDate): Collection
    {
        return OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_amount) as total_amount'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNotIn('status', ['cancelled', 'draft']);
            })
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_amount')
            ->get();
    }

    /**
     * Get sales broken down by time period (daily, weekly, monthly).
     */
    public function getSalesByPeriod(string $startDate, string $endDate, string $period = 'daily'): Collection
    {
        $dateFormat = match ($period) {
            'daily' => 'Y-m-d',
            'weekly' => 'Y-W',
            'monthly' => 'Y-m',
            default => 'Y-m-d',
        };

        return Order::select(
            DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }
}
