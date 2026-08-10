<?php

namespace App\Actions\Receiving;

use App\Enums\Inventory\MovementType;
use App\Enums\Receiving\GRNStatus;
use App\Events\Receiving\GoodsReceived;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Receiving\GoodsReceivedNote;
use App\Models\Receiving\GoodsReceivedNoteItem;
use Illuminate\Support\Facades\DB;

class ReceiveGoodsAction
{
    public function execute(GoodsReceivedNote $grn, GoodsReceivedNoteItem $grnItem, array $data): GoodsReceivedNoteItem
    {
        return DB::transaction(function () use ($grn, $grnItem, $data) {
            $quantityReceived = $data['quantity_received'];
            $quantityAccepted = $data['quantity_accepted'] ?? $quantityReceived;
            $quantityRejected = $data['quantity_rejected'] ?? 0;

            $grnItem->update([
                'quantity_received' => $quantityReceived,
                'quantity_accepted' => $quantityAccepted,
                'quantity_rejected' => $quantityRejected,
                'condition' => $data['condition'] ?? $grnItem->condition,
                'notes' => $data['notes'] ?? $grnItem->notes,
            ]);

            if ($quantityAccepted > 0) {
                $stockItem = StockItem::firstOrCreate(
                    [
                        'company_id' => $grn->company_id,
                        'warehouse_id' => $grn->warehouse_id,
                        'product_id' => $grnItem->product_id,
                        'variant_id' => $grnItem->variant_id,
                    ],
                    [
                        'quantity_on_hand' => 0,
                        'quantity_reserved' => 0,
                        'quantity_on_order' => 0,
                        'status' => 'active',
                        'version' => 0,
                    ]
                );

                $previousQuantity = $stockItem->quantity_on_hand;

                $stockItem->increment('quantity_on_hand', $quantityAccepted);
                $stockItem->update([
                    'last_received_at' => now(),
                ]);
                $stockItem->incrementVersion();

                StockMovement::create([
                    'company_id' => $grn->company_id,
                    'stock_item_id' => $stockItem->id,
                    'movement_type' => MovementType::Receipt,
                    'quantity' => $quantityAccepted,
                    'quantity_before' => $previousQuantity,
                    'quantity_after' => $previousQuantity + $quantityAccepted,
                    'reference_type' => GoodsReceivedNote::class,
                    'reference_id' => $grn->id,
                    'reason' => "Received via GRN #{$grn->grn_number}",
                    'performed_by' => $grn->received_by,
                ]);
            }

            if ($grn->status === GRNStatus::Draft) {
                $grn->update([
                    'status' => GRNStatus::InProgress,
                ]);
            }

            event(new GoodsReceived($grn, $grnItem));

            return $grnItem->fresh();
        });
    }
}
