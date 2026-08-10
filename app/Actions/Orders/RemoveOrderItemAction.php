<?php

namespace App\Actions\Orders;

use App\Events\Orders\OrderItemRemoved;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Facades\DB;

class RemoveOrderItemAction
{
    public function execute(Order $order, OrderItem $orderItem): bool
    {
        return DB::transaction(function () use ($order, $orderItem) {
            $orderItem->delete();

            $this->recalculateOrderTotals($order);

            event(new OrderItemRemoved($order, $orderItem));

            return true;
        });
    }

    private function recalculateOrderTotals(Order $order): void
    {
        $order->load('items');

        $subtotal = $order->items->sum(fn (OrderItem $item) => $item->total_amount - $item->tax_amount);
        $taxAmount = $order->items->sum('tax_amount');
        $totalAmount = $subtotal - $order->discount_amount + $taxAmount + $order->shipping_amount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}
