<?php

namespace App\Observers;

use App\Enums\Delivery\DeliveryStatus;
use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryFailed;
use App\Events\Delivery\DeliveryRescheduled;
use App\Events\Delivery\DeliveryStarted;
use App\Events\Delivery\DriverAssigned;
use App\Models\Delivery\Delivery;
use Spatie\Activitylog\Facades\ActivityLog;

class DeliveryObserver
{
    public function created(Delivery $delivery): void
    {
        ActivityLog::event('Delivery created')
            ->on($delivery)
            ->withProperties([
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'order_id' => $delivery->order_id,
                'customer_id' => $delivery->customer_id,
                'status' => $delivery->status->value,
                'scheduled_date' => $delivery->scheduled_date?->toDateString(),
                'company_id' => $delivery->company_id,
            ])
            ->log();
    }

    public function updated(Delivery $delivery): void
    {
        $changes = $delivery->getChanges();

        ActivityLog::event('Delivery updated')
            ->on($delivery)
            ->withProperties([
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'attributes' => $changes,
                'old' => $delivery->getOriginal(),
            ])
            ->log();

        if (isset($changes['status'])) {
            $oldStatus = DeliveryStatus::tryFrom($delivery->getOriginal('status'));
            $newStatus = DeliveryStatus::tryFrom($changes['status']);

            match ($newStatus) {
                DeliveryStatus::Assigned => DriverAssigned::dispatch($delivery),
                DeliveryStatus::OutForDelivery => DeliveryStarted::dispatch($delivery),
                DeliveryStatus::Delivered => DeliveryCompleted::dispatch($delivery),
                DeliveryStatus::FailedAttempt => DeliveryFailed::dispatch($delivery),
                default => null,
            };
        }

        if (isset($changes['scheduled_date']) && $delivery->status === DeliveryStatus::Pending) {
            DeliveryRescheduled::dispatch($delivery);
        }
    }

    public function deleted(Delivery $delivery): void
    {
        ActivityLog::event('Delivery deleted')
            ->on($delivery)
            ->withProperties([
                'delivery_id' => $delivery->id,
                'delivery_number' => $delivery->delivery_number,
                'order_id' => $delivery->order_id,
            ])
            ->log();
    }

    public function restored(Delivery $delivery): void
    {
        ActivityLog::event('Delivery restored')
            ->on($delivery)
            ->withProperties([
                'delivery_id' => $delivery->id,
            ])
            ->log();
    }
}
