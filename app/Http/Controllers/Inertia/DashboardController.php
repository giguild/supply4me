<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Customers\Customer;
use App\Models\Products\Product;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $company = $user?->company;

        $stats = $company ? [
            'total_orders' => Order::where('company_id', $company->id)->count(),
            'pending_orders' => Order::where('company_id', $company->id)->whereIn('status', ['draft', 'pending'])->count(),
            'total_customers' => Customer::where('company_id', $company->id)->count(),
            'total_products' => Product::where('company_id', $company->id)->count(),
            'pending_payments' => Payment::where('company_id', $company->id)->where('status', 'pending')->count(),
            'monthly_revenue' => Order::where('company_id', $company->id)
                ->where('created_at', '>=', now()->startOfMonth())
                ->where('status', 'completed')
                ->sum('total_amount'),
        ] : [
            'total_orders' => 0,
            'pending_orders' => 0,
            'total_customers' => 0,
            'total_products' => 0,
            'pending_payments' => 0,
            'monthly_revenue' => 0,
        ];

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }
}
