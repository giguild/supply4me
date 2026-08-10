<?php

namespace App\Listeners\Invoicing;

use App\Events\Invoicing\InvoiceGenerated;
use App\Events\Invoicing\InvoiceSent;
use App\Models\Invoicing\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    public function handle(InvoiceGenerated|InvoiceSent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->invoice->load('customer');

        if ($invoice->customer && $invoice->customer->email) {
            Mail::to($invoice->customer->email)->send(new \App\Mail\InvoiceMail($invoice));
        }
    }
}
