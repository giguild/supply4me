<?php

namespace App\Resources\Invoicing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_id' => $this->order_id,
            'customer_id' => $this->customer_id,
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'type' => $this->when(isset($this->type), $this->type),
            'invoice_date' => $this->when(isset($this->invoice_date), $this->invoice_date),
            'due_date' => $this->when(isset($this->due_date), $this->due_date),
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => $this->when(isset($this->tax_amount), (float) $this->tax_amount),
            'discount_amount' => $this->when(isset($this->discount_amount), (float) $this->discount_amount),
            'total_amount' => (float) $this->total_amount,
            'amount_paid' => $this->when(isset($this->amount_paid), (float) $this->amount_paid),
            'amount_due' => $this->when(isset($this->amount_due), (float) $this->amount_due),
            'currency' => $this->when(isset($this->currency), $this->currency),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'terms' => $this->when(isset($this->terms), $this->terms),
            'sent_at' => $this->when(isset($this->sent_at), $this->sent_at),
            'viewed_at' => $this->when(isset($this->viewed_at), $this->viewed_at),
            'paid_at' => $this->when(isset($this->paid_at), $this->paid_at),
            'voided_at' => $this->when(isset($this->voided_at), $this->voided_at),
            'void_reason' => $this->when(isset($this->void_reason), $this->void_reason),
            'pdf_path' => $this->when(isset($this->pdf_path), $this->pdf_path),
            'customer' => new \App\Resources\Customers\CustomerResource($this->whenLoaded('customer')),
            'order' => new \App\Resources\Orders\OrderResource($this->whenLoaded('order')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments' => \App\Resources\Payments\PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
