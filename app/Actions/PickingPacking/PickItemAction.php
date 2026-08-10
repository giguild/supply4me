<?php

namespace App\Actions\PickingPacking;

use App\Enums\PickingPacking\PickItemStatus;
use App\Models\PickingPacking\PickListItem;

class PickItemAction
{
    public function execute(PickListItem $pickListItem, float $quantityPicked, ?string $notes = null): PickListItem
    {
        if ($quantityPicked > $pickListItem->quantity_to_pick) {
            throw new \App\Exceptions\ExceedsPickQuantityException(
                "Picked quantity ({$quantityPicked}) exceeds required quantity ({$pickListItem->quantity_to_pick})."
            );
        }

        $pickListItem->update([
            'quantity_picked' => $quantityPicked,
            'status' => $quantityPicked < $pickListItem->quantity_to_pick
                ? PickItemStatus::Short
                : PickItemStatus::Picked,
            'picked_at' => now(),
            'notes' => $notes ?? $pickListItem->notes,
        ]);

        return $pickListItem->fresh();
    }
}
