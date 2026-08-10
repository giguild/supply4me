<?php

namespace App\Listeners\Notifications;

use App\Events\Core\UserCreated;
use App\Events\Customers\CustomerCreated;
use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderConfirmed;
use App\Events\Orders\OrderCancelled;
use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $emailClass = match (true) {
            $event instanceof UserCreated => \App\Mail\UserCreatedMail::class,
            $event instanceof CustomerCreated => \App\Mail\CustomerCreatedMail::class,
            $event instanceof OrderCreated => \App\Mail\OrderCreatedMail::class,
            $event instanceof OrderConfirmed => \App\Mail\OrderConfirmedMail::class,
            $event instanceof OrderCancelled => \App\Mail\OrderCancelledMail::class,
            $event instanceof PaymentCompleted => \App\Mail\PaymentCompletedMail::class,
            $event instanceof PaymentRejected => \App\Mail\PaymentRejectedMail::class,
            default => null,
        };

        if ($emailClass) {
            $recipient = $this->getRecipient($event);

            if ($recipient) {
                Mail::to($recipient)->send(new $emailClass($event));
            }
        }
    }

    protected function getRecipient(object $event): ?string
    {
        return match (true) {
            $event instanceof UserCreated => $event->user->email,
            $event instanceof CustomerCreated => $event->customer->email,
            $event instanceof OrderCreated => $event->order->customer?->email,
            $event instanceof OrderConfirmed => $event->order->customer?->email,
            $event instanceof OrderCancelled => $event->order->customer?->email,
            $event instanceof PaymentCompleted => $event->payment->customer?->email,
            $event instanceof PaymentRejected => $event->payment->customer?->email,
            default => null,
        };
    }
}
