<?php

namespace App\Actions\Inventory;

use App\Enums\Inventory\MovementType;
use App\Enums\Inventory\TransferItemCondition;
use App\Enums\Inventory\TransferStatus;
use App\Events\Inventory\StockReceived;
use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use Illuminate\Support\Facades\DB;

class ReceiveTransferStockAction
{
    public function execute(StockTransfer $transfer, array $items, User $receivedBy): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $items, $receivedBy) {
            $transfer->load(['items']);

            foreach ($items as $itemData) {
                $transferItem = StockTransferItem::find($itemData['transfer_item_id'] ?? $itemData['stock_transfer_item_id'] ?? null);

                if (! $transferItem || $transferItem->transfer_id !== $transfer->id) {
                    continue;
                }

                $condition = TransferItemCondition::tryFrom($itemData['condition'] ?? 'good') ?? TransferItemCondition::Good;
                $receivedQuantity = (float) ($itemData['received_quantity'] ?? $itemData['quantity_received'] ?? $transferItem->quantity);

                $transferItem->update([
                    'quantity_received' => $receivedQuantity,
                    'condition' => $condition->value,
                    'notes' => $itemData['notes'] ?? $transferItem->notes,
                ]);

                if ($receivedQuantity <= 0 || $condition !== TransferItemCondition::Good) {
                    continue;
                }

                $stockItem = StockItem::firstOrCreate(
                    [
                        'company_id' => $transfer->company_id,
                        'warehouse_id' => $transfer->to_warehouse_id,
                        'product_id' => $transferItem->product_id,
                        'variant_id' => $transferItem->variant_id,
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

                $stockItem->increment('quantity_on_hand', $receivedQuantity);
                $stockItem->incrementVersion();

                StockMovement::create([
                    'company_id' => $transfer->company_id,
                    'stock_item_id' => $stockItem->id,
                    'movement_type' => MovementType::Receipt,
                    'quantity' => $receivedQuantity,
                    'quantity_before' => $previousQuantity,
                    'quantity_after' => $previousQuantity + $receivedQuantity,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'from_warehouse_id' => $transfer->from_warehouse_id,
                    'to_warehouse_id' => $transfer->to_warehouse_id,
                    'reason' => "Received from transfer #{$transfer->transfer_number}",
                    'performed_by' => $receivedBy->id,
                ]);

                $stockItem->loadMissing('warehouse');
                event(new StockReceived($transfer, $receivedBy));
            }

            $transfer->update([
                'status' => TransferStatus::Received,
                'received_at' => now(),
                'received_by' => $receivedBy->id,
            ]);

            return $transfer->fresh();
        });
    }
}