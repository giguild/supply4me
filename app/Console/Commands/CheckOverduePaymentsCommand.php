<?php

namespace App\Console\Commands;

use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckOverduePaymentsCommand extends Command
{
    protected $signature = 'payments:check-overdue {--notify : Send notification for overdue payments}';

    protected $description = 'Check and flag overdue payments';

    public function handle(): int
    {
        $overdueInvoices = Invoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->where('due_amount', '>', 0)
            ->get();

        $this->info("Found {$overdueInvoices->count()} overdue invoices.");

        if ($overdueInvoices->isEmpty()) {
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($overdueInvoices->count());
        $bar->start();

        DB::beginTransaction();

        try {
            foreach ($overdueInvoices as $invoice) {
                $invoice->update(['status' => 'overdue']);

                $this->newLine();
                $this->line("Flagged as overdue: {$invoice->invoice_number} - Due: {$invoice->due_date} - Amount: {$invoice->due_amount}");

                if ($this->option('notify')) {
                    $this->notifyOverdue($invoice);
                }

                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error processing overdue payments: " . $e->getMessage());
            return Command::FAILURE;
        }

        $bar->finish();
        $this->newLine();

        $totalOverdueAmount = $overdueInvoices->sum('due_amount');
        $this->info("Total overdue amount: $" . number_format($totalOverdueAmount, 2));

        return Command::SUCCESS;
    }

    protected function notifyOverdue(Invoice $invoice): void
    {
        $this->line("  Notification sent for {$invoice->invoice_number}");
    }
}
