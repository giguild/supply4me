<?php

namespace App\Listeners\Notifications;

use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderConfirmed;
use App\Events\Orders\OrderCancelled;
use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSMSNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $phoneNumber = $this->getPhoneNumber($event);

        if (!$phoneNumber) {
            return;
        }

        $message = $this->getMessage($event);

        if ($message) {
            Log::info('SMS notification would be sent', [
                'phone' => $phoneNumber,
                'message' => $message,
                'event' => class_basename($event),
            ]);

            // TODO: Integrate with SMS provider (Twilio, Nexmo, etc.)
            // SmsService::send($phoneNumber, $message);
        }
    }

    protected function getPhoneNumber(object $event): ?string
    {
        return match (true) {
            $event instanceof OrderCreated => $event->order->customer?->mobile ?? $event->order->customer?->phone,
            $event instanceof OrderConfirmed => $event->order->customer?->mobile ?? $event->order->customer?->phone,
            $event instanceof OrderCancelled => $event->order->customer?->mobile ?? $event->order->customer?->phone,
            $event instanceof PaymentCompleted => $event->payment->customer?->mobile ?? $event->payment->customer?->phone,
            $event instanceof PaymentRejected => $event->payment->customer?->mobile ?? $event->payment->customer?->phone,
            default => null,
        };
    }

    protected function getMessage(object $event): ?string
    {
        return match (true) {
            $event instanceof OrderCreated => "Your order #{$event->order->order_number} has been received. Total: {$event->order->total_amount}",
            $event instanceof OrderConfirmed => "Your order #{$event->order->order_number} has been confirmed.",
            $event instanceof OrderCancelled => "Your order #{$event->order->order_number} has been cancelled.",
            $event instanceof PaymentCompleted => "Payment of {$event->payment->amount} for order #{$event->payment->order?->order_number} has been received.",
            $event instanceof PaymentRejected => "Payment for order #{$event->payment->order?->order_number} was rejected. Reason: {$event->reason}",
            default => null,
        };
    }
}
