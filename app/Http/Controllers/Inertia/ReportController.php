<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Inventory\StockItem;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use App\Models\Products\Product;
use App\Models\Suppliers\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function sales(Request $request): Response
    {
        $companyId = $request->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalOrders = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalRevenue = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->sum('total_amount');

        $totalInvoices = Invoice::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalPayments = Payment::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $ordersByStatus = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $topCustomers = Order::where('orders.company_id', $companyId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotIn('orders.status', ['cancelled', 'draft'])
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('SUM(orders.total_amount) as total_spent'), DB::raw('COUNT(orders.id) as order_count'))
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $topProducts = \App\Models\Orders\OrderItem::whereHas('order', function ($q) use ($companyId, $startDate, $endDate) {
            $q->where('company_id', $companyId)->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as quantity_sold'), DB::raw('SUM(order_items.line_total) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return Inertia::render('Reports/Sales', [
            'data' => [
                'total_revenue' => $totalRevenue,
                'orders_count' => $totalOrders,
                'average_order_value' => $averageOrderValue,
                'total_invoices' => $totalInvoices,
                'total_payments' => $totalPayments,
                'orders_by_status' => $ordersByStatus,
                'top_customers' => $topCustomers,
                'top_products' => $topProducts,
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    public function inventory(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $totalProducts = Product::where('company_id', $companyId)->count();
        $totalStockItems = StockItem::where('company_id', $companyId)->sum('quantity_on_hand');
        $totalStockValue = (float) (StockItem::where('stock_items.company_id', $companyId)
            ->join('products', 'stock_items.product_id', '=', 'products.id')
            ->selectRaw('SUM(stock_items.quantity_on_hand * CASE WHEN stock_items.cost_price > 0 THEN stock_items.cost_price ELSE products.cost_price END) as total_value')
            ->value('total_value') ?? 0);

        $allStockItems = StockItem::where('company_id', $companyId)
            ->with(['product', 'warehouse'])
            ->get();

        $lowStockItems = $allStockItems->filter(function ($s) {
            $reorderLevel = $s->reorder_level > 0 ? $s->reorder_level : 10;
            return $s->quantity_on_hand <= $reorderLevel;
        });

        $stockByWarehouse = StockItem::where('stock_items.company_id', $companyId)
            ->join('warehouses', 'stock_items.warehouse_id', '=', 'warehouses.id')
            ->select('warehouses.name', DB::raw('SUM(quantity_on_hand) as total_quantity'), DB::raw('SUM(quantity_on_hand * cost_price) as total_value'))
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        $stockByCategory = StockItem::where('stock_items.company_id', $companyId)
            ->join('products', 'stock_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('product_categories.name', DB::raw('SUM(stock_items.quantity_on_hand) as total_quantity'))
            ->groupBy('product_categories.id', 'product_categories.name')
            ->get();

        $stockLevels = $allStockItems->map(function ($item) {
            $effectiveCost = $item->cost_price > 0 ? $item->cost_price : ($item->product->cost_price ?? 0);
            $reorderLevel = $item->reorder_level > 0 ? $item->reorder_level : ($item->product->reorder_level ?? 0);
            return [
                'id' => $item->product->id ?? $item->id,
                'name' => $item->product->name ?? 'Unknown',
                'sku' => $item->product->sku ?? '-',
                'warehouse' => $item->warehouse->name ?? '-',
                'quantity' => $item->quantity_on_hand,
                'min_stock_level' => $reorderLevel,
                'value' => round($item->quantity_on_hand * $effectiveCost, 2),
            ];
        });

        return Inertia::render('Reports/Inventory', [
            'data' => [
                'total_products' => $totalProducts,
                'total_stock_items' => $totalStockItems,
                'low_stock_count' => $lowStockItems->count(),
                'total_value' => $totalStockValue,
                'stock_levels' => $stockLevels,
                'stock_by_warehouse' => $stockByWarehouse,
                'stock_by_category' => $stockByCategory,
            ],
        ]);
    }

    public function financial(Request $request): Response
    {
        $companyId = $request->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalRevenue = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->sum('total_amount');

        $totalInvoiced = Invoice::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalPaid = Payment::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        $totalOutstanding = Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'void')
            ->sum('due_amount');

        $overdueInvoices = Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'void')
            ->where('due_date', '<', now())
            ->with('customer')
            ->get();

        $paymentsByMethod = Payment::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $revenueByMonth = Order::where('company_id', $companyId)
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyBreakdown = $revenueByMonth->map(function ($item) {
            return [
                'name' => $item->month,
                'revenue' => $item->revenue,
                'expenses' => 0,
                'profit' => $item->revenue,
            ];
        });

        return Inertia::render('Reports/Financial', [
            'data' => [
                'revenue' => $totalRevenue,
                'expenses' => 0,
                'profit' => $totalRevenue,
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'monthly_breakdown' => $monthlyBreakdown,
                'overdue_invoices' => $overdueInvoices,
                'payments_by_method' => $paymentsByMethod,
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
