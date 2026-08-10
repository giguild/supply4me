<?php

namespace App\Services\Invoice;

use App\Enums\Invoicing\InvoiceStatus;
use App\Enums\Invoicing\InvoiceType;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\InvoiceItem;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class InvoiceGenerationService
{
    public function __construct(
        private readonly InvoiceCalculationService $calculationService,
    ) {}

    /**
     * Generate an invoice from an order.
     */
    public function generateFromOrder(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $invoice = Invoice::create([
                'company_id' => $order->company_id,
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'type' => InvoiceType::Sales,
                'status' => InvoiceStatus::Draft,
                'currency_code' => $order->currency_code ?? 'USD',
                'due_date' => $order->due_date,
                'notes' => $order->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($order->items as $orderItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $orderItem->product_id,
                    'variant_id' => $orderItem->variant_id,
                    'unit_id' => $orderItem->unit_id,
                    'sku' => $orderItem->sku,
                    'name' => $orderItem->name,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount_percentage' => $orderItem->discount_percentage,
                    'tax_amount' => $orderItem->tax_amount,
                    'total_amount' => $orderItem->total_amount,
                ]);
            }

            $this->calculationService->calculateInvoiceTotals($invoice);

            return $invoice;
        });
    }

    /**
     * Generate a credit note from an existing invoice.
     */
    public function generateCreditNote(Invoice $originalInvoice): Invoice
    {
        return DB::transaction(function () use ($originalInvoice) {
            $creditNote = Invoice::create([
                'company_id' => $originalInvoice->company_id,
                'customer_id' => $originalInvoice->customer_id,
                'order_id' => $originalInvoice->order_id,
                'type' => InvoiceType::CreditNote,
                'status' => InvoiceStatus::Draft,
                'currency_code' => $originalInvoice->currency_code,
                'due_date' => now(),
                'notes' => "Credit note for invoice {$originalInvoice->invoice_number}",
                'created_by' => auth()->id(),
            ]);

            foreach ($originalInvoice->items as $originalItem) {
                InvoiceItem::create([
                    'invoice_id' => $creditNote->id,
                    'product_id' => $originalItem->product_id,
                    'variant_id' => $originalItem->variant_id,
                    'unit_id' => $originalItem->unit_id,
                    'sku' => $originalItem->sku,
                    'name' => $originalItem->name,
                    'quantity' => $originalItem->quantity,
                    'unit_price' => -$originalItem->unit_price,
                    'discount_percentage' => $originalItem->discount_percentage,
                    'tax_amount' => -$originalItem->tax_amount,
                    'total_amount' => -$originalItem->total_amount,
                ]);
            }

            $this->calculationService->calculateInvoiceTotals($creditNote);

            return $creditNote;
        });
    }

    /**
     * Calculate and update invoice totals from its items.
     */
    public function calculateInvoiceTotals(Invoice $invoice): void
    {
        $this->calculationService->calculateInvoiceTotals($invoice);
    }
}
