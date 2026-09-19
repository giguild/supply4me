<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use App\Models\Orders\Order;
use App\Models\Customers\Customer;
use App\Models\Products\Product;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('sales_rep')) {
            return redirect()->route('sales-rep.dashboard');
        }

        $company = $user?->company;

        $can = fn (string $permission): bool => $user->hasPermissionTo($permission);

        $stats = [
            'can_orders' => $can('order.view'),
            'can_customers' => $can('customer.view'),
            'can_products' => $can('product.view') || $can('stock.view'),
            'can_payments' => $can('payment.view'),
            'can_invoices' => $can('invoice.view'),
            'can_deliveries' => $can('delivery.view'),

            'total_orders' => 0,
            'pending_orders' => 0,
            'monthly_revenue' => 0,
            'total_customers' => 0,
            'total_products' => 0,
            'low_stock_count' => 0,
            'pending_payments' => 0,
            'my_deliveries' => 0,
            'active_deliveries' => 0,

            'recent_orders' => [],
            'recent_payments' => [],

            'status_pending' => 0,
            'status_processing' => 0,
            'status_completed' => 0,
            'status_cancelled' => 0,

            'quick_actions' => [],
        ];

        if ($company) {
            $companyId = $company->id;

            if ($can('order.view')) {
                $stats['total_orders'] = Order::where('company_id', $companyId)->count();
                $stats['pending_orders'] = Order::where('company_id', $companyId)
                    ->whereIn('status', ['draft', 'pending'])
                    ->count();
                $stats['monthly_revenue'] = Order::where('company_id', $companyId)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->whereIn('status', ['confirmed', 'processing', 'picking', 'packing', 'ready_to_ship', 'shipped', 'delivered', 'completed'])
                    ->sum('total_amount');
                $stats['recent_orders'] = Order::with('customer')
                    ->where('company_id', $companyId)
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
                    ])
                    ->values();

                // Order status counts for donut chart
                $stats['status_pending'] = Order::where('company_id', $companyId)->whereIn('status', ['pending', 'draft', 'on_hold'])->count();
                $stats['status_processing'] = Order::where('company_id', $companyId)->whereIn('status', ['confirmed', 'processing', 'picking', 'packing', 'ready_to_ship', 'shipped', 'in_transit'])->count();
                $stats['status_completed'] = Order::where('company_id', $companyId)->whereIn('status', ['delivered', 'completed', 'received'])->count();
                $stats['status_cancelled'] = Order::where('company_id', $companyId)->where('status', 'cancelled')->count();
            }

            if ($can('customer.view')) {
                $stats['total_customers'] = Customer::where('company_id', $companyId)->count();
            }

            if ($can('product.view') || $can('stock.view')) {
                $stats['total_products'] = Product::where('company_id', $companyId)->count();
                $stats['low_stock_count'] = Product::where('company_id', $companyId)
                    ->where('reorder_level', '>', 0)
                    ->whereRaw('reorder_level >= (SELECT COALESCE(SUM(quantity_on_hand), 0) FROM stock_items WHERE stock_items.product_id = products.id)')
                    ->count();
            }

            if ($can('payment.view')) {
                $stats['pending_payments'] = Payment::where('company_id', $companyId)
                    ->where('status', 'pending')
                    ->count();
                $stats['recent_payments'] = Payment::with('customer')
                    ->where('company_id', $companyId)
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($payment) => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'reference' => $payment->reference ?? $payment->payment_number ?? $payment->id,
                        'customer' => ['name' => $payment->customer->name ?? 'N/A'],
                        'status' => $payment->status->value ?? $payment->status,
                        'created_at' => $payment->created_at,
                    ])
                    ->values();
            }

            if ($can('delivery.view')) {
                $deliveryQuery = Delivery::where('company_id', $companyId);

                if ($user->hasRole('driver')) {
                    $driver = Driver::where('user_id', $user->id)->first();
                    $deliveryQuery = $deliveryQuery->where('driver_id', $driver?->id);
                }

                $stats['my_deliveries'] = $deliveryQuery->count();
                $stats['active_deliveries'] = (clone $deliveryQuery)
                    ->whereIn('status', ['assigned', 'out_for_delivery'])
                    ->count();
            }
        }

        $actions = [
            'order.create' => ['key' => 'order', 'label' => 'New Order', 'description' => 'Create a purchase order', 'route' => 'orders.create'],
            'customer.create' => ['key' => 'customer', 'label' => 'Add Customer', 'description' => 'Register a new customer', 'route' => 'customers.create'],
            'product.create' => ['key' => 'product', 'label' => 'Add Product', 'description' => 'Add to your inventory', 'route' => 'products.create'],
            'invoice.create' => ['key' => 'invoice', 'label' => 'Create Invoice', 'description' => 'Bill your customers', 'route' => 'invoices.create'],
            'payment.create' => ['key' => 'payment', 'label' => 'Record Payment', 'description' => 'Log an incoming payment', 'route' => 'payments.index'],
            'grn.create' => ['key' => 'grn', 'label' => 'New GRN', 'description' => 'Receive stock from a supplier', 'route' => 'grn.create'],
            'picklist.create' => ['key' => 'picklist', 'label' => 'New Pick List', 'description' => 'Prepare an order for picking', 'route' => 'pick-lists.create'],
            'packinglist.create' => ['key' => 'packinglist', 'label' => 'New Packing List', 'description' => 'Pack a picked order', 'route' => 'packing-lists.create'],
            'shipment.create' => ['key' => 'shipment', 'label' => 'Create Shipment', 'description' => 'Ship out confirmed orders', 'route' => 'shipments.create'],
            'delivery.create' => ['key' => 'delivery', 'label' => 'New Delivery', 'description' => 'Schedule a delivery', 'route' => 'deliveries.create'],
        ];

        $stats['quick_actions'] = collect($actions)
            ->filter(fn ($action, $permission) => $can($permission))
            ->values()
            ->all();

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}