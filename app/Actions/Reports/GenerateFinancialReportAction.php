<?php

namespace App\Actions\Reports;

use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use Carbon\Carbon;

class GenerateFinancialReportAction
{
    public function execute(array $data): array
    {
        $companyId = $data['company_id'];
        $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::now()->startOfMonth();
        $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : Carbon::now()->endOfMonth();

        $invoices = Invoice::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->get();

        $payments = Payment::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->get();

        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $totalOutstanding = $invoices->sum('balance_due');
        $totalRefunded = $payments->where('status', 'refunded')->sum('amount');

        $invoicesByStatus = $invoices->groupBy('status->value')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
            ])
            ->toArray();

        $paymentsByMethod = $payments->where('status', 'completed')
            ->groupBy('method->value')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
            ])
            ->toArray();

        $overdueInvoices = $invoices->filter(function ($invoice) {
            return $invoice->balance_due > 0
                && $invoice->due_date
                && $invoice->due_date->isPast()
                && ! in_array($invoice->status->value, ['paid', 'void', 'cancelled']);
        })->map(fn ($invoice) => [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'customer_id' => $invoice->customer_id,
            'customer_name' => $invoice->customer?->name,
            'total_amount' => $invoice->total_amount,
            'balance_due' => $invoice->balance_due,
            'due_date' => $invoice->due_date->toDateString(),
            'days_overdue' => $invoice->due_date->diffInDays(now()),
        ])->values()->toArray();

        $dailyRevenue = $payments->where('status', 'completed')
            ->groupBy(fn ($payment) => $payment->created_at->toDateString())
            ->map(fn ($group) => $group->sum('amount'))
            ->toArray();

        return [
            'period' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
            'summary' => [
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'total_refunded' => $totalRefunded,
            ],
            'invoices_by_status' => $invoicesByStatus,
            'payments_by_method' => $paymentsByMethod,
            'overdue_invoices' => $overdueInvoices,
            'daily_revenue' => $dailyRevenue,
        ];
    }
}
