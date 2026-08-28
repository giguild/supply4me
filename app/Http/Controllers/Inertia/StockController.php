<?php

namespace App\Http\Controllers\Inertia;

use App\Actions\Inventory\AdjustStockAction;
use App\Actions\Inventory\TransferStockAction;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = StockItem::where('company_id', $companyId)
            ->with(['warehouse', 'product']);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }

        $stockItems = $query->latest()->paginate($request->get('per_page', 15));
        $warehouses = Warehouse::where('company_id', $companyId)->get();

        $allStock = StockItem::where('company_id', $companyId)->get();
        $stats = [
            'total_items' => $allStock->sum('quantity_on_hand'),
            'low_stock' => $allStock->filter(fn ($s) => $s->quantity_on_hand > 0 && $s->quantity_on_hand < $s->reorder_level)->count(),
            'out_of_stock' => $allStock->filter(fn ($s) => $s->quantity_on_hand <= 0)->count(),
            'total_value' => $allStock->sum(fn ($s) => $s->quantity_on_hand * ($s->cost_price ?? 0)),
        ];

        return Inertia::render('Stock/Index', [
            'stockItems' => $stockItems,
            'warehouses' => $warehouses,
            'stats' => $stats,
            'filters' => $request->only(['warehouse_id', 'product_id', 'search']),
        ]);
    }

    public function adjustments(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = StockAdjustment::where('company_id', $companyId)
            ->with(['warehouse', 'performedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('adjustment_number', 'like', "%{$search}%");
        }

        $adjustments = $query->latest()->paginate($request->get('per_page', 15));
        $warehouses = Warehouse::where('company_id', $companyId)->get();

        return Inertia::render('Stock/Adjustments', [
            'adjustments' => $adjustments,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function storeAdjustment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|string|max:50',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_after' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        $companyId = $request->user()->company_id;

        $items = collect($validated['items'])->map(function ($item) use ($companyId) {
            $stockItem = StockItem::where('product_id', $item['product_id'])
                ->where('company_id', $companyId)
                ->first();

            return [
                'product_id' => $item['product_id'],
                'quantity_before' => $stockItem->quantity_on_hand ?? 0,
                'quantity_after' => $item['quantity_after'],
                'reason' => $item['reason'] ?? null,
            ];
        })->toArray();

        app(AdjustStockAction::class)->execute([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'type' => $validated['type'],
            'reason' => $validated['reason'] ?? 'Stock adjustment',
            'notes' => $validated['notes'] ?? null,
            'items' => $items,
        ], $request->user());

        return redirect()->route('stock.adjustments')->with('success', 'Stock adjustment created successfully');
    }

    public function transfers(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = StockTransfer::where('company_id', $companyId)
            ->with(['fromWarehouse', 'toWarehouse', 'shippedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transfer_number', 'like', "%{$search}%");
        }

        $transfers = $query->latest()->paginate($request->get('per_page', 15));
        $warehouses = Warehouse::where('company_id', $companyId)->get();

        return Inertia::render('Stock/Transfers', [
            'transfers' => $transfers,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function storeTransfer(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $companyId = $request->user()->company_id;

        app(TransferStockAction::class)->execute([
            'company_id' => $companyId,
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'notes' => $validated['notes'] ?? null,
            'items' => $validated['items'],
        ], $request->user());

        return redirect()->route('stock.transfers')->with('success', 'Stock transfer created successfully');
    }
}
