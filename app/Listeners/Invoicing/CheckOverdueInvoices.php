<?php

namespace App\Listeners\Invoicing;

use App\Events\Invoicing\InvoiceGenerated;
use App\Events\Invoicing\InvoicePaid;
use App\Enums\Invoicing\InvoiceStatus;
use App\Events\Invoicing\InvoiceOverdue;
use App\Models\Invoicing\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckOverdueInvoices implements ShouldQueue
{
    public function handle(InvoiceGenerated|InvoicePaid $event): void
    {
        $overdueInvoices = Invoice::query()
            ->where('status', InvoiceStatus::Sent)
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $invoice->update(['status' => InvoiceStatus::Overdue]);
            event(new InvoiceOverdue($invoice));
        }
    }
}
