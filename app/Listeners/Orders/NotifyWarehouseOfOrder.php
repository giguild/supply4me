<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderConfirmed;
use App\Mail\GenericNotificationMail;
use App\Models\Core\User;
use App\Models\Orders\Order;
use Illuminate\Support\Facades\Mail;

class NotifyWarehouseOfOrder
{
    public function handle(OrderConfirmed $event): void
    {
        /** @var Order $order */
        $order = $event->order->load('customer');

        $query = User::query()->permission('order.view');

        if ($order->company_id) {
            $query->where('company_id', $order->company_id);
        }

        if ($order->branch_id) {
            $query->where(function ($q) use ($order) {
                $q->whereHas('branches', fn ($b) => $b->whereKey($order->branch_id))
                  ->orWhereDoesntHave('branches');
            });
        }

        $recipients = $query->get();

        foreach ($recipients as $user) {
            if (!$user->email) {
                continue;
            }

            Mail::to($user->email)->send(new GenericNotificationMail([
                'title' => 'New Order Awaiting Processing',
                'message' => "Order #{$order->order_number} has been confirmed and is awaiting processing.",
                'action_url' => route('orders.show', $order->id),
                'action_label' => 'View Order',
            ]));
        }
    }
}