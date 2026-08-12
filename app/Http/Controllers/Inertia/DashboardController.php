<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Customers\Customer;
use App\Models\Products\Product;
use App\Models\Payments\Payment;
use App\Models\Invoicing\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $company = $user?->company;

        if (!$company) {
            return Inertia::render('Dashboard/Index', [
                'user' => $user,
                'stats' => [
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'total_customers' => 0,
                    'total_products' => 0,
                    'pending_payments' => 0,
                    'monthly_revenue' => 0,
                    'low_stock_count' => 0,
                    'recent_orders' => [],
                ],
            ]);
        }

        $stats = [
            'total_orders' => Order::where('company_id', $company->id)->count(),
            'pending_orders' => Order::where('company_id', $company->id)->whereIn('status', ['draft', 'pending'])->count(),
            'total_customers' => Customer::where('company_id', $company->id)->count(),
            'total_products' => Product::where('company_id', $company->id)->count(),
            'pending_payments' => Payment::where('company_id', $company->id)->where('status', 'pending')->count(),
            'monthly_revenue' => Order::where('company_id', $company->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered', 'completed'])
                ->sum('total_amount'),
            'low_stock_count' => Product::where('company_id', $company->id)
                ->where('reorder_level', '>', 0)
                ->whereRaw('reorder_level >= (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM stock_items WHERE stock_items.product_id = products.id)')
                ->count(),
            'recent_orders' => Order::where('company_id', $company->id)
                ->with('customer')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($order) => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => ['name' => $order->customer->name ?? 'N/A'],
                    'status' => $order->status->value ?? $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                ]),
        ];

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
