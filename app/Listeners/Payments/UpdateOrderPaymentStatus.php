<?php

namespace App\Listeners\Payments;

use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentCreated;
use App\Enums\Orders\PaymentStatus;
use App\Models\Orders\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateOrderPaymentStatus implements ShouldQueue
{
    public function handle(PaymentCreated|PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if ($payment->order_id) {
            $order = Order::find($payment->order_id);

            if ($order) {
                $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');

                $paymentStatus = match (true) {
                    $totalPaid >= $order->total_amount => PaymentStatus::Paid,
                    $totalPaid > 0 => PaymentStatus::Partial,
                    default => PaymentStatus::Pending,
                };

                $order->update(['payment_status' => $paymentStatus]);
            }
        }
    }
}
