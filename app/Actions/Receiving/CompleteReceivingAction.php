<?php

namespace App\Actions\Receiving;

use App\Enums\Receiving\GRNStatus;
use App\Events\Receiving\GRNCompleted;
use App\Models\Receiving\GoodsReceivedNote;
use Illuminate\Support\Facades\DB;

class CompleteReceivingAction
{
    public function execute(GoodsReceivedNote $grn): GoodsReceivedNote
    {
        return DB::transaction(function () use ($grn) {
            $grn->load('items');

            $hasUnreceivedItems = $grn->items->contains(function ($item) {
                return $item->quantity_received < $item->quantity_ordered;
            });

            if ($hasUnreceivedItems) {
                throw new \App\Exceptions\GRNIncompleteException(
                    'Cannot complete GRN with unreceived items.'
                );
            }

            $grn->update([
                'status' => GRNStatus::Completed,
            ]);

            event(new GRNCompleted($grn));

            return $grn->fresh();
        });
    }
}
