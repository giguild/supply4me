<?php

namespace App\Events\Payments;

use App\Models\Core\User;
use App\Models\Payments\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentRefunded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Payment $payment,
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('company.'.$this->payment->company_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'payment.refunded';
    }

    public function broadcastWith(): array
    {
        return [
            'payment' => [
                'id' => $this->payment->id,
                'payment_number' => $this->payment->payment_number,
                'amount' => $this->payment->amount,
                'status' => $this->payment->status->value,
            ],
            'refunded_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
