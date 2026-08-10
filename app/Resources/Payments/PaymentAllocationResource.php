<?php

namespace App\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentAllocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'invoice_id' => $this->invoice_id,
            'amount' => (float) $this->amount,
            'invoice' => new \App\Resources\Invoicing\InvoiceResource($this->whenLoaded('invoice')),
            'created_at' => $this->created_at,
        ];
    }
}
