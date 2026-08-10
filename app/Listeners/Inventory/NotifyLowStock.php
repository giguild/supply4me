<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use App\Models\Inventory\StockItem;
use App\Models\Core\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyLowStock implements ShouldQueue
{
    public function handle(StockLow|StockOut $event): void
    {
        /** @var StockItem $stockItem */
        $stockItem = $event->stockItem->load(['warehouse.users', 'product']);

        $warehouseUsers = $stockItem->warehouse->users ?? collect();

        foreach ($warehouseUsers as $warehouseUser) {
            $warehouseUser->notify(new \App\Notifications\LowStockNotification($stockItem));
        }

        $adminUsers = User::query()
            ->where('company_id', $stockItem->company_id)
            ->where('department', 'management')
            ->get();

        foreach ($adminUsers as $adminUser) {
            $adminUser->notify(new \App\Notifications\LowStockAdminNotification($stockItem));
        }
    }
}
