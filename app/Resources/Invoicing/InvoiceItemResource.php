<?php

namespace App\Resources\Invoicing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'order_item_id' => $this->when(isset($this->order_item_id), $this->order_item_id),
            'product_id' => $this->product_id,
            'description' => $this->when(isset($this->description), $this->description),
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount' => $this->when(isset($this->discount), (float) $this->discount),
            'tax_rate' => $this->when(isset($this->tax_rate), (float) $this->tax_rate),
            'tax_amount' => $this->when(isset($this->tax_amount), (float) $this->tax_amount),
            'line_total' => (float) $this->line_total,
            'product' => new \App\Resources\Products\ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
