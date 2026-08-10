<?php

namespace App\Console\Commands;

use App\Models\Companies\Company;
use App\Models\Invoicing\Invoice;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyReportsCommand extends Command
{
    protected $signature = 'reports:generate-daily {--date=today : Date to generate report for (Y-m-d)}';

    protected $description = 'Generate daily sales and financial reports for all active companies';

    public function handle(): int
    {
        $date = $this->option('date') === 'today' ? now()->toDateString() : $this->option('date');

        $this->info("Generating daily reports for {$date}...");

        $companies = Company::where('status', 'active')->get();

        $bar = $this->output->createProgressBar($companies->count());
        $bar->start();

        foreach ($companies as $company) {
            $this->generateSalesReport($company, $date);
            $this->generateFinancialReport($company, $date);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Daily reports generated successfully for " . $companies->count() . " companies.");

        return Command::SUCCESS;
    }

    protected function generateSalesReport(Company $company, string $date): void
    {
        $orders = Order::where('company_id', $company->id)
            ->whereDate('created_at', $date)
            ->get();

        $totalOrders = $orders->count();
        $totalRevenue = $orders->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalCancelled = $orders->where('status', 'cancelled')->count();

        Log::info("Sales report for {$company->name} on {$date}", [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'cancelled_orders' => $totalCancelled,
        ]);

        $this->line("  Sales Report: {$totalOrders} orders, $" . number_format($totalRevenue, 2) . " revenue");
    }

    protected function generateFinancialReport(Company $company, string $date): void
    {
        $payments = Payment::where('company_id', $company->id)
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->get();

        $invoices = Invoice::where('company_id', $company->id)
            ->whereDate('created_at', $date)
            ->get();

        $totalPaymentsReceived = $payments->where('payment_type', 'incoming')->sum('amount');
        $totalPaymentsMade = $payments->where('payment_type', 'outgoing')->sum('amount');
        $totalInvoiced = $invoices->sum('total_amount');

        Log::info("Financial report for {$company->name} on {$date}", [
            'payments_received' => $totalPaymentsReceived,
            'payments_made' => $totalPaymentsMade,
            'total_invoiced' => $totalInvoiced,
        ]);

        $this->line("  Financial Report: $" . number_format($totalPaymentsReceived, 2) . " received, $" . number_format($totalPaymentsMade, 2) . " paid");
    }
}
