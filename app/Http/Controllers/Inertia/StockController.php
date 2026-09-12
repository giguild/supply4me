<?php

namespace App\Http\Controllers\Inertia;

use App\Actions\Inventory\AdjustStockAction;
use App\Actions\Inventory\ReceiveTransferStockAction;
use App\Actions\Inventory\TransferStockAction;
use App\Enums\Inventory\MovementType;
use App\Enums\Inventory\TransferStatus;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\Warehouse;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
            ->with(['warehouse', 'performedBy', 'items.product']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('adjustment_number', 'like', "%{$search}%");
        }

        $adjustments = $query->latest()->paginate($request->get('per_page', 15));

        $adjustments->getCollection()->transform(function (StockAdjustment $adjustment) {
            $firstItem = $adjustment->items->first();
            $adjustment->setAttribute('quantity', (float) $adjustment->items->sum('difference'));
            $adjustment->setAttribute('product', $firstItem?->product);
            $adjustment->setAttribute('user', $adjustment->performedBy);

            return $adjustment;
        });

        $warehouses = Warehouse::where('company_id', $companyId)->get();

        return Inertia::render('Stock/Adjustments', [
            'adjustments' => $adjustments,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function adjustmentsCreate(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('Stock/AdjustmentsCreate', [
            'warehouses' => Warehouse::where('company_id', $companyId)->get(),
            'products' => Product::where('company_id', $companyId)->get(),
        ]);
    }

    public function storeAdjustment(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|in:cycle_count,physical_count,damage,expiry,shrinkage,other',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_after' => 'required|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        $companyId = $request->user()->company_id;

        $errors = [];
        $items = [];

        foreach ($validated['items'] as $index => $item) {
            $stockItem = StockItem::where('product_id', $item['product_id'])
                ->where('warehouse_id', $validated['warehouse_id'])
                ->where('company_id', $companyId)
                ->first();

            if (! $stockItem) {
                $errors["items.{$index}.product_id"] = 'No stock item exists for this product in the selected warehouse.';
                continue;
            }

            $items[] = [
                'product_id' => $item['product_id'],
                'quantity_before' => $stockItem->quantity_on_hand,
                'quantity_after' => $item['quantity_after'],
                'reason' => $item['reason'] ?? null,
            ];
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

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

    public function approveAdjustment(Request $request, StockAdjustment $adjustment): \Illuminate\Http\RedirectResponse
    {
        if ($adjustment->company_id !== $request->user()->company_id) {
            abort(403);
        }

        if ($adjustment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending adjustments can be approved.');
        }

        $adjustment->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', "Adjustment {$adjustment->adjustment_number} approved.");
    }

    public function rejectAdjustment(Request $request, StockAdjustment $adjustment): \Illuminate\Http\RedirectResponse
    {
        if ($adjustment->company_id !== $request->user()->company_id) {
            abort(403);
        }

        if ($adjustment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending adjustments can be rejected.');
        }

        $adjustment->load('items');

        DB::transaction(function () use ($adjustment, $request) {
            foreach ($adjustment->items as $item) {
                $stockItem = StockItem::where('product_id', $item->product_id)
                    ->where('warehouse_id', $adjustment->warehouse_id)
                    ->where('company_id', $adjustment->company_id)
                    ->first();

                if (! $stockItem) {
                    continue;
                }

                $previousQuantity = $stockItem->quantity_on_hand;
                $restoreTo = (float) $item->quantity_before;

                if (abs($previousQuantity - $restoreTo) > 0.0001) {
                    $stockItem->update(['quantity_on_hand' => $restoreTo]);
                    $stockItem->incrementVersion();

                    StockMovement::create([
                        'company_id' => $stockItem->company_id,
                        'stock_item_id' => $stockItem->id,
                        'movement_type' => MovementType::Adjustment,
                        'quantity' => abs($previousQuantity - $restoreTo),
                        'quantity_before' => $previousQuantity,
                        'quantity_after' => $restoreTo,
                        'reference_type' => StockAdjustment::class,
                        'reference_id' => $adjustment->id,
                        'reason' => "Reversed rejected adjustment #{$adjustment->adjustment_number}",
                        'performed_by' => $request->user()->id,
                    ]);
                }
            }

            $adjustment->update([
                'status' => 'rejected',
                'rejected_by' => $request->user()->id,
                'rejected_at' => now(),
                'rejection_reason' => $request->input('reason') ?? 'Rejected',
            ]);
        });

        return redirect()->back()->with('success', "Adjustment {$adjustment->adjustment_number} rejected and stock restored.");
    }

    public function transfers(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $query = StockTransfer::where('company_id', $companyId)
            ->with(['fromWarehouse', 'toWarehouse', 'items.product', 'shippedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transfer_number', 'like', "%{$search}%");
        }

        $transfers = $query->latest()->paginate($request->get('per_page', 15));

        $transfers->getCollection()->transform(function (StockTransfer $transfer) {
            $firstItem = $transfer->items->first();
            $transfer->setAttribute('quantity', (float) $transfer->items->sum('quantity'));
            $transfer->setAttribute('product', $firstItem?->product);

            return $transfer;
        });

        $warehouses = Warehouse::where('company_id', $companyId)->get();

        return Inertia::render('Stock/Transfers', [
            'transfers' => $transfers,
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function transfersCreate(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('Stock/TransfersCreate', [
            'warehouses' => Warehouse::where('company_id', $companyId)->get(),
            'products' => Product::where('company_id', $companyId)->get(),
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

        $errors = [];
        foreach ($validated['items'] as $index => $item) {
            $stockItem = StockItem::where('product_id', $item['product_id'])
                ->where('warehouse_id', $validated['from_warehouse_id'])
                ->where('company_id', $companyId)
                ->first();

            if (! $stockItem) {
                $errors["items.{$index}.product_id"] = 'No stock item exists for this product in the source warehouse.';
                continue;
            }

            $available = (float) $stockItem->quantity_on_hand - (float) $stockItem->quantity_reserved;
            if ($available < (float) $item['quantity']) {
                $errors["items.{$index}.quantity"] = "Insufficient stock. Available: {$available}";
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        app(TransferStockAction::class)->execute([
            'company_id' => $companyId,
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'notes' => $validated['notes'] ?? null,
            'items' => $validated['items'],
        ], $request->user());

        return redirect()->route('stock.transfers')->with('success', 'Stock transfer created successfully');
    }

    public function approveTransfer(Request $request, StockTransfer $transfer): \Illuminate\Http\RedirectResponse
    {
        if ($transfer->company_id !== $request->user()->company_id) {
            abort(403);
        }

        if ($transfer->status !== TransferStatus::PendingApproval) {
            return redirect()->back()->with('error', 'Only pending transfers can be approved.');
        }

        $transfer->update([
            'status' => TransferStatus::Approved,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', "Transfer {$transfer->transfer_number} approved.");
    }

    public function shipTransfer(Request $request, StockTransfer $transfer): \Illuminate\Http\RedirectResponse
    {
        if ($transfer->company_id !== $request->user()->company_id) {
            abort(403);
        }

        if ($transfer->status !== TransferStatus::Approved) {
            return redirect()->back()->with('error', 'Only approved transfers can be shipped.');
        }

        $transfer->update([
            'status' => TransferStatus::InTransit,
            'shipped_at' => now(),
        ]);

        return redirect()->back()->with('success', "Transfer {$transfer->transfer_number} shipped.");
    }

    public function receiveTransfer(Request $request, StockTransfer $transfer): \Illuminate\Http\RedirectResponse
    {
        if ($transfer->company_id !== $request->user()->company_id) {
            abort(403);
        }

        if ($transfer->status !== TransferStatus::InTransit) {
            return redirect()->back()->with('error', 'Only in-transit transfers can be received.');
        }

        $transfer->load('items');

        $items = $transfer->items
            ->filter(fn ($item) => $item->quantity_received === null || $item->quantity_received < $item->quantity)
            ->map(fn ($item) => [
                'transfer_item_id' => $item->id,
                'received_quantity' => (float) $item->quantity,
                'condition' => 'good',
            ])
            ->values()
            ->all();

        if (empty($items)) {
            return redirect()->back()->with('error', 'Transfer has no outstanding items to receive.');
        }

        app(ReceiveTransferStockAction::class)->execute($transfer, $items, $request->user());

        return redirect()->back()->with('success', "Transfer {$transfer->transfer_number} received and stock added.");
    }
}