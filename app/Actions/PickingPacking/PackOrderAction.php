<?php

namespace App\Actions\PickingPacking;

use App\Enums\Orders\OrderStatus;
use App\Enums\PickingPacking\PackingStatus;
use App\Enums\PickingPacking\PickListStatus;
use App\Events\PickingPacking\OrderPacked;
use App\Exceptions\OrderAlreadyPackedException;
use App\Exceptions\OrderNotPickedException;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PackingList;
use App\Models\PickingPacking\PackingListItem;

class PackOrderAction
{
    public function execute(Order $order, array $data): PackingList
    {
        $order->load('items.product', 'pickList');

        $pickList = $order->pickList;

        if (($pickList?->status ?? null) !== PickListStatus::Completed) {
            throw new OrderNotPickedException('This order must be picked before it can be packed.');
        }

        if (PackingList::where('order_id', $order->id)->exists()) {
            throw new OrderAlreadyPackedException('This order has already been packed.');
        }

        $packingList = PackingList::create([
            'company_id' => $order->company_id,
            'order_id' => $order->id,
            'pick_list_id' => $data['pick_list_id'] ?? $pickList?->id,
            'warehouse_id' => $order->warehouse_id
                ?? \App\Models\Inventory\Warehouse::where('company_id', $order->company_id)->value('id'),
            'status' => PackingStatus::InProgress,
            'packer_id' => $data['packer_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($order->items as $orderItem) {
            PackingListItem::create([
                'packing_list_id' => $packingList->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'variant_id' => $orderItem->variant_id,
                'quantity' => $orderItem->quantity,
                'weight' => $orderItem->product->weight ?? null,
                'dimensions' => $orderItem->product->dimensions ?? null,
                'package_type' => $data['package_type'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
            ]);
        }

        $packingList->update([
            'status' => PackingStatus::Packed,
            'completed_at' => now(),
        ]);

        $order->update(['status' => OrderStatus::ReadyToShip]);

        event(new OrderPacked($packingList));

        return $packingList;
    }
}
