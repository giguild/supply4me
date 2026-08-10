<?php

namespace App\Listeners\Payments;

use App\Events\Payments\PaymentCompleted;
use App\Enums\Invoicing\InvoiceStatus;
use App\Models\Invoicing\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateInvoicePaymentStatus implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if ($payment->invoice_id) {
            $invoice = Invoice::find($payment->invoice_id);

            if ($invoice) {
                $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
                $balanceDue = $invoice->total_amount - $totalPaid;

                $invoice->update([
                    'amount_paid' => $totalPaid,
                    'balance_due' => max(0, $balanceDue),
                    'status' => $balanceDue <= 0 ? InvoiceStatus::Paid : InvoiceStatus::PartiallyPaid,
                    'paid_at' => $balanceDue <= 0 ? now() : $invoice->paid_at,
                ]);
            }
        }
    }
}
