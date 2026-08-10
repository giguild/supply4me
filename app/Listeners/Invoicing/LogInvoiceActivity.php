<?php

namespace App\Listeners\Invoicing;

use App\Events\Invoicing\InvoiceGenerated;
use App\Events\Invoicing\InvoiceOverdue;
use App\Events\Invoicing\InvoicePaid;
use App\Events\Invoicing\InvoiceSent;
use App\Events\Invoicing\InvoiceVoided;
use App\Models\Invoicing\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Activitylog\Facades\Activity;

class LogInvoiceActivity implements ShouldQueue
{
    public function handle(InvoiceGenerated|InvoiceSent|InvoicePaid|InvoiceOverdue|InvoiceVoided $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->invoice;
        $eventName = class_basename($event);

        $properties = [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'total_amount' => $invoice->total_amount,
            'status' => $invoice->status->value,
            'event' => $eventName,
        ];

        Activity::performedOn($invoice)
            ->event('invoice.'.strtolower(str_replace('Invoice', '', $eventName)))
            ->withProperties($properties)
            ->log("Invoice {$eventName}: {$invoice->invoice_number}");
    }
}
