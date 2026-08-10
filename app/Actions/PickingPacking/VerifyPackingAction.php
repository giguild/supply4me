<?php

namespace App\Actions\PickingPacking;

use App\Enums\PickingPacking\PackingStatus;
use App\Events\PickingPacking\PackingVerified;
use App\Models\PickingPacking\PackingList;

class VerifyPackingAction
{
    public function execute(PackingList $packingList, ?string $notes = null): PackingList
    {
        if ($packingList->status !== PackingStatus::Packed) {
            throw new \App\Exceptions\PackingCannotBeVerifiedException(
                'Packing list can only be verified from packed status.'
            );
        }

        $packingList->update([
            'status' => PackingStatus::Verified,
            'notes' => $notes ? ($packingList->notes ? $packingList->notes . "\n" . $notes : $notes) : $packingList->notes,
        ]);

        event(new PackingVerified($packingList));

        return $packingList->fresh();
    }
}
