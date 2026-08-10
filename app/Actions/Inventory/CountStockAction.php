<?php

namespace App\Actions\Inventory;

use App\Enums\Inventory\AdjustmentType;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockAdjustmentItem;
use App\Models\Inventory\StockItem;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;

class CountStockAction
{
    public function execute(array $data, User $user): StockAdjustment
    {
        return DB::transaction(function () use ($data, $user) {
            $adjustment = StockAdjustment::create([
                'company_id' => $data['company_id'],
                'warehouse_id' => $data['warehouse_id'],
                'type' => AdjustmentType::CycleCount,
                'reason' => $data['reason'] ?? 'Stock count',
                'status' => 'pending',
                'performed_by' => $user->id,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $stockItem = StockItem::where('product_id', $itemData['product_id'])
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->where('company_id', $data['company_id'])
                    ->first();

                if (! $stockItem) {
                    continue;
                }

                $quantityBefore = $stockItem->quantity_on_hand;
                $countedQuantity = $itemData['counted_quantity'];
                $difference = $countedQuantity - $quantityBefore;

                StockAdjustmentItem::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $itemData['product_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $countedQuantity,
                    'difference' => $difference,
                    'reason' => 'Stock count',
                    'bin_id' => $itemData['bin_id'] ?? null,
                ]);

                if ($difference != 0) {
                    $stockItem->update([
                        'quantity_on_hand' => $countedQuantity,
                        'last_counted_at' => now(),
                    ]);

                    $stockItem->incrementVersion();
                } else {
                    $stockItem->update([
                        'last_counted_at' => now(),
                    ]);
                }
            }

            return $adjustment;
        });
    }
}
