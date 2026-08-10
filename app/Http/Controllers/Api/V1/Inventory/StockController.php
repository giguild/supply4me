<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Resources\Inventory\StockItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stock = StockItem::query()
            ->with(['product', 'warehouse'])
            ->when($request->search, function ($q, $s) {
                $q->whereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            })
            ->when($request->warehouse_id, fn ($q, $w) => $q->where('warehouse_id', $w))
            ->when($request->product_id, fn ($q, $p) => $q->where('product_id', $p))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->low_stock, function ($q) {
                $q->whereColumn('quantity_on_hand', '<=', 'minimum_stock_level');
            })
            ->paginate($request->get('per_page', 15));

        return $this->paginated($stock, StockItemResource::collection($stock->items()));
    }

    public function show(StockItem $stockItem): JsonResponse
    {
        return $this->success(
            new StockItemResource($stockItem->load(['product', 'warehouse']))
        );
    }

    public function movements(Request $request): JsonResponse
    {
        $movements = StockMovement::query()
            ->with(['stockItem.product', 'stockItem.warehouse'])
            ->when($request->stock_item_id, fn ($q, $s) => $q->where('stock_item_id', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->date_from, fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('created_at', '<=', $d))
            ->latest()
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'message' => 'Stock movements retrieved successfully',
            'data' => $movements,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $query = StockItem::query()->with(['product', 'warehouse']);

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $stock = $query->get();

        return $this->success([
            'total_products' => $stock->unique('product_id')->count(),
            'total_value' => $stock->sum(fn ($item) => $item->quantity_on_hand * $item->product->cost_price),
            'total_quantity' => $stock->sum('quantity_on_hand'),
            'low_stock_count' => $stock->filter(fn ($item) => $item->quantity_on_hand <= $item->minimum_stock_level)->count(),
            'out_of_stock_count' => $stock->filter(fn ($item) => $item->quantity_on_hand <= 0)->count(),
        ]);
    }
}
