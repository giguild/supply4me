<?php

namespace App\Observers;

use App\Enums\Invoicing\InvoiceStatus;
use App\Events\Invoicing\InvoiceGenerated;
use App\Events\Invoicing\InvoiceOverdue;
use App\Events\Invoicing\InvoicePaid;
use App\Events\Invoicing\InvoiceSent;
use App\Events\Invoicing\InvoiceVoided;
use App\Models\Invoicing\Invoice;
use Spatie\Activitylog\Facades\ActivityLog;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        ActivityLog::event('Invoice generated')
            ->on($invoice)
            ->withProperties([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_id' => $invoice->customer_id,
                'total_amount' => $invoice->total_amount,
                'status' => $invoice->status->value,
                'company_id' => $invoice->company_id,
            ])
            ->log();

        InvoiceGenerated::dispatch($invoice);

        $invoice->statusHistory()->create([
            'invoice_id' => $invoice->id,
            'status' => $invoice->status,
            'comment' => 'Invoice created',
        ]);
    }

    public function updated(Invoice $invoice): void
    {
        $changes = $invoice->getChanges();

        ActivityLog::event('Invoice updated')
            ->on($invoice)
            ->withProperties([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'attributes' => $changes,
                'old' => $invoice->getOriginal(),
            ])
            ->log();

        if (isset($changes['status'])) {
            $oldStatus = InvoiceStatus::tryFrom($invoice->getOriginal('status'));
            $newStatus = InvoiceStatus::tryFrom($changes['status']);

            $invoice->statusHistory()->create([
                'invoice_id' => $invoice->id,
                'status' => $newStatus,
                'comment' => "Status changed from {$oldStatus->value} to {$newStatus->value}",
            ]);

            match ($newStatus) {
                InvoiceStatus::Sent => InvoiceSent::dispatch($invoice),
                InvoiceStatus::Paid => InvoicePaid::dispatch($invoice),
                InvoiceStatus::Void => InvoiceVoided::dispatch($invoice),
                default => null,
            };

            if ($newStatus !== InvoiceStatus::Paid && $invoice->due_date && $invoice->due_date->isPast()) {
                InvoiceOverdue::dispatch($invoice);
            }
        }

        if (isset($changes['balance_due']) && $invoice->balance_due <= 0 && $invoice->status !== InvoiceStatus::Paid) {
            $invoice->updateQuietly(['status' => InvoiceStatus::Paid, 'paid_at' => now()]);
        }
    }

    public function deleted(Invoice $invoice): void
    {
        ActivityLog::event('Invoice deleted')
            ->on($invoice)
            ->withProperties([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
            ])
            ->log();
    }

    public function restored(Invoice $invoice): void
    {
        ActivityLog::event('Invoice restored')
            ->on($invoice)
            ->withProperties([
                'invoice_id' => $invoice->id,
            ])
            ->log();
    }
}
