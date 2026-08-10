<?php

namespace App\Actions\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Facades\DB;

class UpdateOrderItemAction
{
    public function execute(OrderItem $orderItem, array $data): OrderItem
    {
        return DB::transaction(function () use ($orderItem, $data) {
            $orderItem->update([
                'product_id' => $data['product_id'] ?? $orderItem->product_id,
                'variant_id' => $data['variant_id'] ?? $orderItem->variant_id,
                'unit_id' => $data['unit_id'] ?? $orderItem->unit_id,
                'sku' => $data['sku'] ?? $orderItem->sku,
                'name' => $data['name'] ?? $orderItem->name,
                'quantity' => $data['quantity'] ?? $orderItem->quantity,
                'unit_price' => $data['unit_price'] ?? $orderItem->unit_price,
                'discount_percentage' => $data['discount_percentage'] ?? $orderItem->discount_percentage,
                'notes' => $data['notes'] ?? $orderItem->notes,
            ]);

            $quantity = $data['quantity'] ?? $orderItem->quantity;
            $unitPrice = $data['unit_price'] ?? $orderItem->unit_price;
            $discountPercentage = $data['discount_percentage'] ?? $orderItem->discount_percentage;

            $itemTotal = $quantity * $unitPrice;
            $itemDiscount = $discountPercentage > 0 ? $itemTotal * ($discountPercentage / 100) : 0;
            $itemTax = ($itemTotal - $itemDiscount) * ($orderItem->tax_amount / max($itemTotal - $itemDiscount, 1));

            $orderItem->update([
                'tax_amount' => $itemTax,
                'total_amount' => $itemTotal - $itemDiscount + $itemTax,
            ]);

            $this->recalculateOrderTotals($orderItem->order);

            return $orderItem->fresh();
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
