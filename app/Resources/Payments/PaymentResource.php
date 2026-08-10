<?php

namespace App\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'customer_id' => $this->customer_id,
            'amount' => (float) $this->amount,
            'payment_method' => $this->when(isset($this->payment_method), $this->payment_method),
            'payment_date' => $this->when(isset($this->payment_date), $this->payment_date),
            'reference_number' => $this->when(isset($this->reference_number), $this->reference_number),
            'bank_name' => $this->when(isset($this->bank_name), $this->bank_name),
            'bank_account' => $this->when(isset($this->bank_account), $this->bank_account),
            'check_number' => $this->when(isset($this->check_number), $this->check_number),
            'status' => $this->when(isset($this->status), $this->status),
            'status_label' => $this->when(isset($this->status), fn () => $this->status->label()),
            'type' => $this->when(isset($this->type), $this->type),
            'notes' => $this->when(isset($this->notes), $this->notes),
            'refund_amount' => $this->when(isset($this->refund_amount), (float) $this->refund_amount),
            'refund_reason' => $this->when(isset($this->refund_reason), $this->refund_reason),
            'approved_at' => $this->when(isset($this->approved_at), $this->approved_at),
            'rejected_at' => $this->when(isset($this->rejected_at), $this->rejected_at),
            'rejection_reason' => $this->when(isset($this->rejection_reason), $this->rejection_reason),
            'receipt_path' => $this->when(isset($this->receipt_path), $this->receipt_path),
            'customer' => new \App\Resources\Customers\CustomerResource($this->whenLoaded('customer')),
            'allocations' => PaymentAllocationResource::collection($this->whenLoaded('allocations')),
            'approved_by' => new \App\Resources\Core\UserResource($this->whenLoaded('approvedBy')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
