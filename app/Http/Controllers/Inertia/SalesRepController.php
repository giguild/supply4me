<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SalesRepController extends Controller
{
    public function adminIndex(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $salesReps = User::where('company_id', $companyId)
            ->role('sales_rep')
            ->withCount('assignedCustomers')
            ->latest()
            ->get();

        $customerCounts = Customer::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereNotNull('assigned_to')
            ->select('assigned_to', DB::raw('count(*) as total'))
            ->groupBy('assigned_to')
            ->pluck('total', 'assigned_to');

        $orderStats = Order::where('orders.company_id', $companyId)
            ->whereNotIn('orders.status', ['cancelled', 'draft'])
            ->whereNotNull('orders.customer_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereNotNull('customers.assigned_to')
            ->select(
                'customers.assigned_to',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('AVG(orders.total_amount) as avg_order_value')
            )
            ->groupBy('customers.assigned_to')
            ->get()
            ->keyBy('assigned_to');

        $paymentStats = Payment::where('payments.company_id', $companyId)
            ->whereNotNull('payments.customer_id')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->whereNotNull('customers.assigned_to')
            ->select(
                'customers.assigned_to',
                DB::raw('COUNT(payments.id) as total_payments'),
                DB::raw('SUM(CASE WHEN payments.status IN ("approved", "completed") THEN payments.amount ELSE 0 END) as collected_amount'),
                DB::raw('SUM(CASE WHEN payments.status = "pending" THEN 1 ELSE 0 END) as pending_payments'),
                DB::raw('SUM(CASE WHEN payments.status = "rejected" THEN 1 ELSE 0 END) as rejected_payments')
            )
            ->groupBy('customers.assigned_to')
            ->get()
            ->keyBy('assigned_to');

        $reps = $salesReps->map(function (User $rep) use ($customerCounts, $orderStats, $paymentStats) {
            $orders = $orderStats[$rep->id] ?? null;
            $payments = $paymentStats[$rep->id] ?? null;

            $totalPayments = $payments->total_payments ?? 0;
            $collected = $payments->collected_amount ?? 0;
            $completed = ($payments->pending_payments ?? 0) + ($payments->rejected_payments ?? 0);
            $completionRate = $totalPayments > 0
                ? round((($totalPayments - $completed) / $totalPayments) * 100, 1)
                : 0;

            return [
                'id' => $rep->id,
                'name' => $rep->name,
                'email' => $rep->email,
                'phone' => $rep->phone,
                'region' => $rep->region,
                'state' => $rep->state,
                'status' => $rep->status,
                'total_customers' => $rep->assigned_customers_count,
                'active_customers' => $customerCounts[$rep->id] ?? 0,
                'total_orders' => $orders->total_orders ?? 0,
                'total_revenue' => round($orders->total_revenue ?? 0, 2),
                'avg_order_value' => round($orders->avg_order_value ?? 0, 2),
                'total_payments' => $totalPayments,
                'collected_amount' => round($collected, 2),
                'pending_payments' => $payments->pending_payments ?? 0,
                'rejected_payments' => $payments->rejected_payments ?? 0,
                'payment_completion_rate' => $completionRate,
            ];
        });

        return Inertia::render('SalesRep/AdminIndex', [
            'salesReps' => $reps,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Request $request, User $user): Response
    {
        $companyId = $request->user()->company_id;

        $customerCounts = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->where('status', 'active')
            ->count();

        $totalCustomers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->count();

        $orderStats = Order::where('orders.company_id', $companyId)
            ->whereHas('customer', fn ($q) => $q->where('assigned_to', $user->id))
            ->whereNotIn('orders.status', ['cancelled', 'draft'])
            ->select(
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('AVG(orders.total_amount) as avg_order_value')
            )
            ->first();

        $paymentStats = Payment::where('payments.company_id', $companyId)
            ->whereNotNull('payments.customer_id')
            ->join('customers', 'payments.customer_id', '=', 'customers.id')
            ->where('customers.assigned_to', $user->id)
            ->select(
                DB::raw('COUNT(payments.id) as total_payments'),
                DB::raw('SUM(CASE WHEN payments.status IN ("approved", "completed") THEN payments.amount ELSE 0 END) as collected_amount'),
                DB::raw('SUM(CASE WHEN payments.status = "pending" THEN 1 ELSE 0 END) as pending_payments')
            )
            ->first();

        $recentOrders = Order::where('orders.company_id', $companyId)
            ->whereHas('customer', fn ($q) => $q->where('assigned_to', $user->id))
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        $customers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->withCount(['orders' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'draft'])])
            ->withSum('orders', 'total_amount')
            ->latest()
            ->paginate(15);

        return Inertia::render('SalesRep/Show', [
            'rep' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'region' => $user->region,
                'state' => $user->state,
                'status' => $user->status,
                'created_at' => $user->created_at,
            ],
            'stats' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $customerCounts,
                'total_orders' => $orderStats->total_orders ?? 0,
                'total_revenue' => round($orderStats->total_revenue ?? 0, 2),
                'avg_order_value' => round($orderStats->avg_order_value ?? 0, 2),
                'total_payments' => $paymentStats->total_payments ?? 0,
                'collected_amount' => round($paymentStats->collected_amount ?? 0, 2),
                'pending_payments' => $paymentStats->pending_payments ?? 0,
            ],
            'recentOrders' => $recentOrders,
            'customers' => $customers,
        ]);
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $customers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->with('orders', fn ($q) => $q->latest()->limit(5));

        if ($request->filled('search')) {
            $search = $request->search;
            $customers->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $customers->where('status', $request->status);
        }

        $customers = $customers->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('SalesRep/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function dashboard(Request $request): Response
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $totalCustomers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->count();

        $activeCustomers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->where('status', 'active')
            ->count();

        $totalOrders = Order::where('company_id', $companyId)
            ->whereHas('customer', fn ($q) => $q->where('assigned_to', $user->id))
            ->count();

        $totalRevenue = Order::where('company_id', $companyId)
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->whereHas('customer', fn ($q) => $q->where('assigned_to', $user->id))
            ->sum('total_amount');

        $recentCustomers = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentOrders = Order::where('company_id', $companyId)
            ->whereHas('customer', fn ($q) => $q->where('assigned_to', $user->id))
            ->with('customer')
            ->latest()
            ->limit(5)
            ->get();

        $customersByStatus = Customer::where('company_id', $companyId)
            ->where('assigned_to', $user->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('SalesRep/Dashboard', [
            'stats' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
            ],
            'recentCustomers' => $recentCustomers,
            'recentOrders' => $recentOrders,
            'customersByStatus' => $customersByStatus,
        ]);
    }
}
