<?php

namespace App\Actions\Receiving;

use App\Enums\Receiving\GRNStatus;
use App\Events\Receiving\GRNCreated;
use App\Models\Receiving\GoodsReceivedNote;
use App\Models\Receiving\GoodsReceivedNoteItem;

class CreateGRNAction
{
    public function execute(array $data): GoodsReceivedNote
    {
        $grn = GoodsReceivedNote::create([
            'company_id' => $data['company_id'],
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'warehouse_id' => $data['warehouse_id'],
            'status' => GRNStatus::Draft,
            'received_date' => $data['received_date'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? null,
            'received_by' => $data['received_by'] ?? null,
            'checked_by' => $data['checked_by'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);

        if (! empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                GoodsReceivedNoteItem::create([
                    'grn_id' => $grn->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'] ?? null,
                    'product_id' => $itemData['product_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'bin_id' => $itemData['bin_id'] ?? null,
                    'quantity_ordered' => $itemData['quantity_ordered'] ?? 0,
                    'quantity_received' => 0,
                    'quantity_accepted' => 0,
                    'quantity_rejected' => 0,
                    'condition' => $itemData['condition'] ?? 'good',
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        }

        event(new GRNCreated($grn));

        return $grn;
    }
}
