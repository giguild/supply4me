<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockAdjustmentItem;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StockItem::where('company_id', $request->user()->company_id)
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
        $warehouses = Warehouse::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Inventory/StockLevels', [
            'stockItems' => $stockItems,
            'warehouses' => $warehouses,
            'filters' => $request->only(['warehouse_id', 'product_id', 'search']),
        ]);
    }

    public function adjustments(Request $request): Response
    {
        $query = StockAdjustment::where('company_id', $request->user()->company_id)
            ->with(['warehouse', 'performedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('adjustment_number', 'like', "%{$search}%");
        }

        $adjustments = $query->latest()->paginate($request->get('per_page', 15));
        $warehouses = Warehouse::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Inventory/Adjustments', [
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
            'items.*.quantity_before' => 'required|numeric|min:0',
            'items.*.quantity_after' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        $companyId = $request->user()->company_id;

        $adjustment = StockAdjustment::create([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'type' => $validated['type'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'performed_by' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            StockAdjustmentItem::create([
                'adjustment_id' => $adjustment->id,
                'product_id' => $item['product_id'],
                'quantity_before' => $item['quantity_before'],
                'quantity_after' => $item['quantity_after'],
                'difference' => $item['quantity_after'] - $item['quantity_before'],
                'reason' => $item['reason'] ?? null,
            ]);
        }

        return redirect()->route('stock.adjustments')->with('success', 'Stock adjustment created successfully');
    }

    public function transfers(Request $request): Response
    {
        $query = StockTransfer::where('company_id', $request->user()->company_id)
            ->with(['fromWarehouse', 'toWarehouse', 'shippedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transfer_number', 'like', "%{$search}%");
        }

        $transfers = $query->latest()->paginate($request->get('per_page', 15));
        $warehouses = Warehouse::where('company_id', $request->user()->company_id)->get();

        return Inertia::render('Inventory/Transfers', [
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

        $transfer = StockTransfer::create([
            'company_id' => $companyId,
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'status' => 'pending',
            'shipped_by' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            StockTransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'quantity_received' => 0,
            ]);
        }

        return redirect()->route('stock.transfers')->with('success', 'Stock transfer created successfully');
    }
}
