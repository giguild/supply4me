<?php

namespace App\Services\Reporting;

use App\Enums\Invoicing\InvoiceStatus;
use App\Enums\Payments\PaymentStatus;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Get revenue report for a date range.
     */
    public function getRevenueReport(string $startDate, string $endDate): array
    {
        $invoices = Invoice::whereIn('status', [InvoiceStatus::Paid, InvoiceStatus::Partial])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_invoices' => $invoices->count(),
            'total_revenue' => $invoices->sum('total_amount'),
            'total_collected' => $invoices->sum('amount_paid'),
            'total_outstanding' => $invoices->sum('balance_due'),
            'average_invoice_value' => $invoices->count() > 0
                ? $invoices->sum('total_amount') / $invoices->count()
                : 0,
        ];
    }

    /**
     * Get payments report for a date range.
     */
    public function getPaymentsReport(string $startDate, string $endDate): Collection
    {
        return Payment::where('status', PaymentStatus::Completed)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('customer')
            ->orderByDesc('payment_date')
            ->get();
    }

    /**
     * Get outstanding invoices report.
     */
    public function getOutstandingReport(): array
    {
        $outstanding = Invoice::whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::Sent, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->where('balance_due', '>', 0)
            ->with('customer')
            ->get();

        $byStatus = $outstanding->groupBy('status')->map(function ($invoices, $status) {
            return [
                'count' => $invoices->count(),
                'total' => $invoices->sum('balance_due'),
            ];
        });

        return [
            'total_outstanding' => $outstanding->sum('balance_due'),
            'total_invoices' => $outstanding->count(),
            'by_status' => $byStatus,
            'invoices' => $outstanding,
        ];
    }

    /**
     * Get accounts receivable aging report.
     */
    public function getAgingReport(): Collection
    {
        $invoices = Invoice::whereIn('status', [InvoiceStatus::Pending, InvoiceStatus::Sent, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->where('balance_due', '>', 0)
            ->with('customer')
            ->get();

        return $invoices->map(function (Invoice $invoice) {
            $daysOverdue = $invoice->due_date
                ? max(0, $invoice->due_date->diffInDays(now()))
                : 0;

            return [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
                'customer_name' => $invoice->customer?->name,
                'total_amount' => (float) $invoice->total_amount,
                'balance_due' => (float) $invoice->balance_due,
                'due_date' => $invoice->due_date?->toDateString(),
                'days_overdue' => $daysOverdue,
                'aging_bucket' => $this->getAgingBucket($daysOverdue),
            ];
        })->sortByDesc('days_overdue')->values();
    }

    /**
     * Categorize invoices into aging buckets.
     */
    private function getAgingBucket(int $days): string
    {
        return match (true) {
            $days <= 0 => 'Current',
            $days <= 30 => '1-30 days overdue',
            $days <= 60 => '31-60 days overdue',
            $days <= 90 => '61-90 days overdue',
            default => '90+ days overdue',
        };
    }
}
