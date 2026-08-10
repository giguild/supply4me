<?php

namespace App\Actions\Reports;

use App\Models\Orders\Order;
use Carbon\Carbon;

class GenerateSalesReportAction
{
    public function execute(array $data): array
    {
        $companyId = $data['company_id'];
        $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::now()->startOfMonth();
        $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : Carbon::now()->endOfMonth();

        $orders = Order::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $statusBreakdown = $orders->groupBy('status->value')
            ->map(fn ($group) => $group->count())
            ->toArray();

        $dailySales = $orders->groupBy(fn ($order) => $order->created_at->toDateString())
            ->map(fn ($group) => [
                'count' => $group->count(),
                'revenue' => $group->sum('total_amount'),
            ])
            ->toArray();

        $topCustomers = $orders->groupBy('customer_id')
            ->map(fn ($group) => [
                'customer_id' => $group->first()->customer_id,
                'customer_name' => $group->first()->customer?->name,
                'order_count' => $group->count(),
                'total_revenue' => $group->sum('total_amount'),
            ])
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values()
            ->toArray();

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'average_order_value' => round($averageOrderValue, 2),
            ],
            'status_breakdown' => $statusBreakdown,
            'daily_sales' => $dailySales,
            'top_customers' => $topCustomers,
        ];
    }
}
