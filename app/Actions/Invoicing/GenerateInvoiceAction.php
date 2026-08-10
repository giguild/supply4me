<?php

namespace App\Actions\Invoicing;

use App\Enums\Invoicing\InvoiceStatus;
use App\Enums\Invoicing\InvoiceType;
use App\Events\Invoicing\InvoiceGenerated;
use App\Models\Core\User;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\InvoiceItem;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\DB;

class GenerateInvoiceAction
{
    public function execute(Order $order, User $createdBy): Invoice
    {
        return DB::transaction(function () use ($order, $createdBy) {
            $order->load('items');

            $invoice = Invoice::create([
                'company_id' => $order->company_id,
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'type' => InvoiceType::Sales,
                'status' => InvoiceStatus::Draft,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'amount_paid' => 0,
                'balance_due' => $order->total_amount,
                'currency_code' => $order->currency_code,
                'due_date' => $order->due_date,
                'notes' => $order->notes,
                'created_by' => $createdBy->id,
                'metadata' => $order->metadata,
            ]);

            foreach ($order->items as $orderItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $orderItem->product_id,
                    'variant_id' => $orderItem->variant_id,
                    'unit_id' => $orderItem->unit_id,
                    'sku' => $orderItem->sku,
                    'name' => $orderItem->name,
                    'description' => $orderItem->notes,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount_percentage' => $orderItem->discount_percentage,
                    'tax_amount' => $orderItem->tax_amount,
                    'total_amount' => $orderItem->total_amount,
                ]);
            }

            event(new InvoiceGenerated($invoice));

            return $invoice;
        });
    }
}
