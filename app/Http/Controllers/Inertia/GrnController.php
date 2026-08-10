<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
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
        $purchaseOrders = PurchaseOrder::where('company_id', $companyId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with('supplier')
            ->get();

        return Inertia::render('Receiving/Create', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_ordered' => 'nullable|numeric|min:0',
            'items.*.quantity_received' => 'required|numeric|min:0',
            'items.*.quantity_accepted' => 'required|numeric|min:0',
            'items.*.quantity_rejected' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|string|max:50',
            'items.*.notes' => 'nullable|string',
        ]);

        $companyId = $request->user()->company_id;

        $grn = GoodsReceivedNote::create([
            'company_id' => $companyId,
            'purchase_order_id' => $validated['purchase_order_id'] ?? null,
            'supplier_id' => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'status' => 'received',
            'received_date' => $validated['received_date'],
            'notes' => $validated['notes'] ?? null,
            'received_by' => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            GoodsReceivedNoteItem::create([
                'grn_id' => $grn->id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'] ?? 0,
                'quantity_received' => $item['quantity_received'],
                'quantity_accepted' => $item['quantity_accepted'],
                'quantity_rejected' => $item['quantity_rejected'] ?? 0,
                'condition' => $item['condition'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('grn.index')->with('success', 'GRN created successfully');
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
            foreach ($validated['items'] as $item) {
                GoodsReceivedNoteItem::where('id', $item['id'])->update(
                    array_filter([
                        'quantity_received' => $item['quantity_received'] ?? null,
                        'quantity_accepted' => $item['quantity_accepted'] ?? null,
                        'quantity_rejected' => $item['quantity_rejected'] ?? null,
                        'condition' => $item['condition'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ], fn ($v) => $v !== null)
                );
            }
        }

        return redirect()->route('grn.show', $grn)->with('success', 'GRN updated successfully');
    }
}
