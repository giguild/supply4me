<?php

namespace App\Listeners\Inventory;

use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use App\Mail\GenericNotificationMail;
use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use Illuminate\Support\Facades\Mail;

class NotifyLowStock
{
    public function handle(StockLow|StockOut $event): void
    {
        /** @var StockItem $stockItem */
        $stockItem = $event->stockItem->load('product');

        $isOut = $event instanceof StockOut;

        $title = $isOut ? 'Stock Out Alert' : 'Low Stock Alert';
        $message = $isOut
            ? "{$stockItem->product?->name} is out of stock."
            : "{$stockItem->product?->name} is running low. Current stock: {$stockItem->quantity_on_hand}.";

        $recipients = collect();

        $query = User::query()->permission('stock.view');

        if ($stockItem->company_id) {
            $query->where('company_id', $stockItem->company_id);
        }

        $recipients = $recipients->merge($query->get());

        $management = User::query()
            ->when($stockItem->company_id, fn ($q) => $q->where('company_id', $stockItem->company_id))
            ->where('department', 'management')
            ->get();

        $recipients = $recipients->merge($management)->unique('id');

        foreach ($recipients as $user) {
            if (!$user->email) {
                continue;
            }

            Mail::to($user->email)->send(new GenericNotificationMail([
                'title' => $title,
                'message' => $message,
                'action_url' => $stockItem->product ? route('products.show', $stockItem->product->id) : null,
                'action_label' => 'View Product',
            ]));
        }
    }
}