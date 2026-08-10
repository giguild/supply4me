<?php

namespace App\Actions\Invoicing;

use App\Enums\Invoicing\InvoiceStatus;
use App\Events\Invoicing\InvoiceVoided;
use App\Models\Core\User;
use App\Models\Invoicing\Invoice;
use Illuminate\Support\Facades\DB;

class VoidInvoiceAction
{
    public function execute(Invoice $invoice, User $user, ?string $reason = null): Invoice
    {
        if ($invoice->status === InvoiceStatus::Void) {
            throw new \App\Exceptions\InvoiceAlreadyVoidedException(
                'Invoice is already void.'
            );
        }

        if ($invoice->status === InvoiceStatus::Paid) {
            throw new \App\Exceptions\InvoiceCannotBeVoidedException(
                'Paid invoices cannot be void.'
            );
        }

        return DB::transaction(function () use ($invoice, $user, $reason) {
            $invoice->update([
                'status' => InvoiceStatus::Void,
                'balance_due' => 0,
                'notes' => $reason ? ($invoice->notes ? $invoice->notes . "\nVoid reason: " . $reason : "Void reason: " . $reason) : $invoice->notes,
            ]);

            $invoice->statusHistory()->create([
                'status' => InvoiceStatus::Void,
                'notes' => $reason ?? 'Invoice voided',
                'changed_by' => $user->id,
            ]);

            event(new InvoiceVoided($invoice, $user));

            return $invoice->fresh();
        });
    }
}
