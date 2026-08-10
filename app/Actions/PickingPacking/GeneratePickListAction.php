<?php

namespace App\Actions\PickingPacking;

use App\Enums\PickingPacking\PickItemStatus;
use App\Enums\PickingPacking\PickListStatus;
use App\Events\PickingPacking\PickListGenerated;
use App\Models\Inventory\StockItem;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PickList;
use App\Models\PickingPacking\PickListItem;

class GeneratePickListAction
{
    public function execute(Order $order): PickList
    {
        $order->load('items.product');

        $pickList = PickList::create([
            'company_id' => $order->company_id,
            'warehouse_id' => $order->warehouse_id,
            'order_id' => $order->id,
            'status' => PickListStatus::Pending,
        ]);

        foreach ($order->items as $orderItem) {
            $stockItem = StockItem::where('product_id', $orderItem->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->where('company_id', $order->company_id)
                ->first();

            PickListItem::create([
                'pick_list_id' => $pickList->id,
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'variant_id' => $orderItem->variant_id,
                'bin_id' => $stockItem?->bin_id,
                'quantity_to_pick' => $orderItem->quantity,
                'quantity_picked' => 0,
                'status' => PickItemStatus::Pending,
            ]);
        }

        event(new PickListGenerated($pickList));

        return $pickList;
    }
}
