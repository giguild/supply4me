<?php

namespace App\Observers;

use App\Enums\Orders\OrderStatus;
use App\Events\Orders\OrderCancelled;
use App\Events\Orders\OrderConfirmed;
use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderStatusChanged;
use App\Models\Orders\Order;
use Spatie\Activitylog\Facades\ActivityLog;

class OrderObserver
{
    public function created(Order $order): void
    {
        ActivityLog::event('Order created')
            ->on($order)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'total_amount' => $order->total_amount,
                'status' => $order->status->value,
                'company_id' => $order->company_id,
            ])
            ->log();

        $order->statusHistory()->create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => 'Order created',
        ]);
    }

    public function updated(Order $order): void
    {
        $changes = $order->getChanges();

        ActivityLog::event('Order updated')
            ->on($order)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'attributes' => $changes,
                'old' => $order->getOriginal(),
            ])
            ->log();

        if (isset($changes['status'])) {
            $oldStatus = OrderStatus::tryFrom($order->getOriginal('status'));
            $newStatus = OrderStatus::tryFrom($changes['status']);

            $order->statusHistory()->create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'comment' => "Status changed from {$oldStatus->value} to {$newStatus->value}",
            ]);

            OrderStatusChanged::dispatch($order, $oldStatus, $newStatus);

            match ($newStatus) {
                OrderStatus::Confirmed => OrderConfirmed::dispatch($order),
                OrderStatus::Cancelled => OrderCancelled::dispatch($order),
                default => null,
            };
        }
    }

    public function deleted(Order $order): void
    {
        ActivityLog::event('Order deleted')
            ->on($order)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
            ])
            ->log();
    }

    public function restored(Order $order): void
    {
        ActivityLog::event('Order restored')
            ->on($order)
            ->withProperties([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ])
            ->log();
    }
}
