<?php

namespace App\Actions\Inventory;

use App\Enums\Inventory\AdjustmentType;
use App\Enums\Inventory\MovementType;
use App\Events\Inventory\StockAdjusted;
use App\Models\Core\User;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockAdjustmentItem;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use Illuminate\Support\Facades\DB;

class AdjustStockAction
{
    public function execute(array $data, User $user): StockAdjustment
    {
        return DB::transaction(function () use ($data, $user) {
            $adjustment = StockAdjustment::create([
                'company_id' => $data['company_id'],
                'warehouse_id' => $data['warehouse_id'],
                'type' => AdjustmentType::from($data['type']),
                'reason' => $data['reason'],
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
                    throw new \App\Exceptions\StockItemNotFoundException(
                        "Stock item not found for product {$itemData['product_id']}"
                    );
                }

                $quantityBefore = $stockItem->quantity_on_hand;
                $difference = ($itemData['quantity_after'] ?? 0) - $quantityBefore;

                StockAdjustmentItem::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id' => $itemData['product_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $itemData['quantity_after'] ?? $quantityBefore,
                    'difference' => $difference,
                    'reason' => $itemData['reason'] ?? $data['reason'],
                    'bin_id' => $itemData['bin_id'] ?? null,
                ]);

                $stockItem->update([
                    'quantity_on_hand' => $itemData['quantity_after'] ?? $quantityBefore,
                ]);

                $stockItem->incrementVersion();

                StockMovement::create([
                    'company_id' => $stockItem->company_id,
                    'stock_item_id' => $stockItem->id,
                    'movement_type' => MovementType::Adjustment,
                    'quantity' => abs($difference),
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $itemData['quantity_after'] ?? $quantityBefore,
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'reason' => $data['reason'],
                    'performed_by' => $user->id,
                ]);
            }

            event(new StockAdjusted($adjustment, $user));

            return $adjustment;
        });
    }
}
