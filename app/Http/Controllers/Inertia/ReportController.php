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
            ->where('status', 'completed')
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

        $topCustomers = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
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
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.total_amount) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return Inertia::render('Reports/Sales', [
            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'total_invoices' => $totalInvoices,
                'total_payments' => $totalPayments,
            ],
            'ordersByStatus' => $ordersByStatus,
            'topCustomers' => $topCustomers,
            'topProducts' => $topProducts,
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
        $totalStockValue = StockItem::where('company_id', $companyId)
            ->selectRaw('SUM(quantity_on_hand * cost_price) as total_value')
            ->value('total_value') ?? 0;

        $lowStockItems = StockItem::where('company_id', $companyId)
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
            ->with(['product', 'warehouse'])
            ->get();

        $stockByWarehouse = StockItem::where('company_id', $companyId)
            ->join('warehouses', 'stock_items.warehouse_id', '=', 'warehouses.id')
            ->select('warehouses.name', DB::raw('SUM(quantity_on_hand) as total_quantity'), DB::raw('SUM(quantity_on_hand * cost_price) as total_value'))
            ->groupBy('warehouses.id', 'warehouses.name')
            ->get();

        $stockByCategory = StockItem::where('company_id', $companyId)
            ->join('products', 'stock_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('product_categories.name', DB::raw('SUM(stock_items.quantity_on_hand) as total_quantity'))
            ->groupBy('product_categories.id', 'product_categories.name')
            ->get();

        return Inertia::render('Reports/Inventory', [
            'summary' => [
                'total_products' => $totalProducts,
                'total_stock_items' => $totalStockItems,
                'total_stock_value' => $totalStockValue,
            ],
            'lowStockItems' => $lowStockItems,
            'stockByWarehouse' => $stockByWarehouse,
            'stockByCategory' => $stockByCategory,
        ]);
    }

    public function financial(Request $request): Response
    {
        $companyId = $request->user()->company_id;
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $totalRevenue = Order::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
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
            ->where('status', '!=', 'voided')
            ->sum('balance_due');

        $overdueInvoices = Invoice::where('company_id', $companyId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'voided')
            ->where('due_date', '<', now())
            ->with('customer')
            ->get();

        $paymentsByMethod = Payment::where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get();

        $revenueByMonth = Order::where('company_id', $companyId)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->selectRaw('SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Reports/Financial', [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
            ],
            'overdueInvoices' => $overdueInvoices,
            'paymentsByMethod' => $paymentsByMethod,
            'revenueByMonth' => $revenueByMonth,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
