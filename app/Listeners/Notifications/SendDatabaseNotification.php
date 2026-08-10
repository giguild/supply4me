<?php

namespace App\Listeners\Notifications;

use App\Events\Core\UserCreated;
use App\Events\Core\UserDeactivated;
use App\Events\Core\UserUpdated;
use App\Events\Customers\CustomerCreated;
use App\Events\Customers\CustomerUpdated;
use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderConfirmed;
use App\Events\Orders\OrderCancelled;
use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentRejected;
use App\Models\Core\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;

class SendDatabaseNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $eventName = class_basename($event);

        $notificationClass = match (true) {
            $event instanceof UserCreated => new \App\Notifications\UserCreatedNotification($event->user),
            $event instanceof UserUpdated => new \App\Notifications\UserUpdatedNotification($event->user),
            $event instanceof UserDeactivated => new \App\Notifications\UserDeactivatedNotification($event->user),
            $event instanceof CustomerCreated => new \App\Notifications\CustomerCreatedNotification($event->customer),
            $event instanceof CustomerUpdated => new \App\Notifications\CustomerUpdatedNotification($event->customer),
            $event instanceof OrderCreated => new \App\Notifications\OrderCreatedNotification($event->order),
            $event instanceof OrderConfirmed => new \App\Notifications\OrderConfirmedNotification($event->order),
            $event instanceof OrderCancelled => new \App\Notifications\OrderCancelledNotification($event->order),
            $event instanceof PaymentCompleted => new \App\Notifications\PaymentCompletedNotification($event->payment),
            $event instanceof PaymentRejected => new \App\Notifications\PaymentRejectedNotification($event->payment),
            default => null,
        };

        if ($notificationClass) {
            $companyUsers = User::query()
                ->where('company_id', $event->order?->company_id ?? $event->customer?->company_id ?? $event->user?->company_id)
                ->where('id', '!=', auth()->id())
                ->get();

            foreach ($companyUsers as $user) {
                $user->notify($notificationClass);
            }
        }
    }
}
