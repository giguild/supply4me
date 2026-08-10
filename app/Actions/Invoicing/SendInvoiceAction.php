<?php

namespace App\Actions\Invoicing;

use App\Enums\Invoicing\InvoiceStatus;
use App\Events\Invoicing\InvoiceSent;
use App\Models\Core\User;
use App\Models\Invoicing\Invoice;

class SendInvoiceAction
{
    public function execute(Invoice $invoice, User $user): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Draft && $invoice->status !== InvoiceStatus::Pending) {
            throw new \App\Exceptions\InvoiceCannotBeSentException(
                'Invoice can only be sent from draft or pending status.'
            );
        }

        $invoice->update([
            'status' => InvoiceStatus::Sent,
        ]);

        event(new InvoiceSent($invoice, $user));

        return $invoice->fresh();
    }
}
