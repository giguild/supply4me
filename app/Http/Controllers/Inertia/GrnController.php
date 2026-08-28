<?php

namespace App\Http\Controllers\Inertia;

use App\Actions\Receiving\ReceiveGoodsAction;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use App\Models\PurchaseOrders\PurchaseOrder;
use App\Models\Receiving\GoodsReceivedNote;
use App\Models\Receiving\GoodsReceivedNoteItem;
use App\Models\Suppliers\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GrnController extends Controller
{
    public function index(Request $request): Response
    {
        $query = GoodsReceivedNote::where('company_id', $request->user()->company_id)
            ->withCount('items')
            ->with(['supplier', 'warehouse', 'purchaseOrder', 'receivedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('grn_number', 'like', "%{$search}%");
        }

        $grns = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Receiving/Index', [
            'grns' => $grns,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $suppliers = Supplier::where('company_id', $companyId)->get();
        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $products = Product::where('company_id', $companyId)->get();
        $purchaseOrders = PurchaseOrder::where('company_id', $companyId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('supplier')
            ->get();

        return Inertia::render('Receiving/Create', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'products' => $products,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'receiving_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.expected_quantity' => 'nullable|numeric|min:0',
            'items.*.received_quantity' => 'required|numeric|min:0',
            'items.*.accepted_quantity' => 'required|numeric|min:0',
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|in:good,damaged,expired,wrong_item',
            'items.*.notes' => 'nullable|string',
        ]);

        $companyId = $request->user()->company_id;

        $grn = GoodsReceivedNote::create([
            'company_id' => $companyId,
            'purchase_order_id' => $validated['purchase_order_id'] ?? null,
            'supplier_id' => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'status' => 'accepted',
            'receiving_date' => $validated['receiving_date'],
            'notes' => $validated['notes'] ?? null,
            'received_by' => $request->user()->id,
        ]);

        $receiveGoods = app(ReceiveGoodsAction::class);

        foreach ($validated['items'] as $item) {
            $grnItem = GoodsReceivedNoteItem::create([
                'grn_id' => $grn->id,
                'product_id' => $item['product_id'],
                'expected_quantity' => $item['expected_quantity'] ?? 0,
                'received_quantity' => $item['received_quantity'],
                'accepted_quantity' => $item['accepted_quantity'],
                'rejected_quantity' => $item['rejected_quantity'] ?? 0,
                'condition' => $item['condition'] ?? 'good',
                'notes' => $item['notes'] ?? null,
            ]);

            $receiveGoods->execute($grn, $grnItem, [
                'quantity_received' => $item['received_quantity'],
                'quantity_accepted' => $item['accepted_quantity'],
                'quantity_rejected' => $item['rejected_quantity'] ?? 0,
                'condition' => $item['condition'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('grn.index')->with('success', 'GRN created and stock updated successfully');
    }

    public function show(Request $request, GoodsReceivedNote $grn): Response
    {
        $grn->load([
            'supplier',
            'warehouse',
            'purchaseOrder',
            'items.product',
            'receivedBy',
            'checkedBy',
        ]);

        return Inertia::render('Receiving/Show', [
            'grn' => $grn,
        ]);
    }

    public function update(Request $request, GoodsReceivedNote $grn): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'required_with:items|exists:goods_received_note_items,id',
            'items.*.quantity_received' => 'nullable|numeric|min:0',
            'items.*.quantity_accepted' => 'nullable|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|string|max:50',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($request->filled('status')) {
            $grn->update(['status' => $validated['status']]);
        }

        if ($request->filled('notes')) {
            $grn->update(['notes' => $validated['notes']]);
        }

        if ($request->filled('items')) {
            $receiveGoods = app(ReceiveGoodsAction::class);

            foreach ($validated['items'] as $item) {
                $grnItem = GoodsReceivedNoteItem::find($item['id']);
                if ($grnItem) {
                    $receiveGoods->execute($grn, $grnItem, [
                        'quantity_received' => $item['quantity_received'] ?? $grnItem->quantity_received,
                        'quantity_accepted' => $item['quantity_accepted'] ?? $grnItem->quantity_accepted,
                        'quantity_rejected' => $item['quantity_rejected'] ?? $grnItem->quantity_rejected,
                        'condition' => $item['condition'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('grn.show', $grn)->with('success', 'GRN updated successfully');
    }
}
