<?php

namespace App\Actions\Orders;

use App\Events\Orders\OrderItemAdded;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Facades\DB;

class AddOrderItemAction
{
    public function execute(Order $order, array $data): OrderItem
    {
        return DB::transaction(function () use ($order, $data) {
            $itemTotal = $data['quantity'] * $data['unit_price'];
            $itemDiscount = ($data['discount_percentage'] ?? 0) > 0
                ? $itemTotal * ($data['discount_percentage'] / 100)
                : 0;
            $itemTax = ($itemTotal - $itemDiscount) * (($data['tax_rate'] ?? 0) / 100);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $data['product_id'],
                'variant_id' => $data['variant_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'sku' => $data['sku'] ?? null,
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'discount_percentage' => $data['discount_percentage'] ?? 0,
                'tax_amount' => $itemTax,
                'total_amount' => $itemTotal - $itemDiscount + $itemTax,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->recalculateOrderTotals($order);

            event(new OrderItemAdded($order, $orderItem));

            return $orderItem;
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
