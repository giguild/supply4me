<?php

namespace App\Listeners\Payments;

use App\Events\Payments\PaymentCompleted;
use App\Models\Payments\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmation implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        /** @var Payment $payment */
        $payment = $event->payment->load('customer');

        if ($payment->customer && $payment->customer->email) {
            Mail::to($payment->customer->email)->send(new \App\Mail\PaymentReceiptMail($payment));
        }
    }
}
