<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderStatusChanged;
use App\Enums\Orders\OrderStatus;
use App\Models\Orders\Order;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateOrderInvoice implements ShouldQueue
{
    public function handle(OrderStatusChanged $event): void
    {
        /** @var Order $order */
        $order = $event->order;

        if ($event->newStatus === OrderStatus::Completed) {
            $invoiceService = app(\App\Services\InvoiceService::class);
            $invoiceService->generateFromOrder($order);
        }
    }
}
