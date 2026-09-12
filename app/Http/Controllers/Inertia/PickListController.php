<?php

namespace App\Http\Controllers\Inertia;

use App\Actions\PickingPacking\GeneratePickListAction;
use App\Actions\PickingPacking\PickItemAction;
use App\Enums\PickingPacking\PickItemStatus;
use App\Enums\PickingPacking\PickListStatus;
use App\Exceptions\OrderAlreadyPickedException;
use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PickList;
use App\Models\PickingPacking\PickListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PickListController extends Controller
{
    public function __construct(
        protected GeneratePickListAction $generatePickListAction,
        protected PickItemAction $pickItemAction
    ) {}

    public function index(Request $request): Response
    {
        $query = PickList::where('company_id', $request->user()->company_id)
            ->with(['order', 'warehouse', 'picker']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('pick_list_number', 'like', "%{$search}%");
        }

        $pickLists = $query->latest()->paginate($request->get('per_page', 15));

        $stats = [
            'awaiting_pick' => Order::where('company_id', $request->user()->company_id)
                ->whereIn('status', ['confirmed', 'processing'])
                ->whereDoesntHave('pickList')
                ->count(),
            'pending_lists' => PickList::where('company_id', $request->user()->company_id)
                ->whereIn('status', [PickListStatus::Draft->value, PickListStatus::Pending->value])
                ->count(),
            'in_progress_lists' => PickList::where('company_id', $request->user()->company_id)
                ->where('status', PickListStatus::InProgress->value)
                ->count(),
            'completed_lists' => PickList::where('company_id', $request->user()->company_id)
                ->where('status', PickListStatus::Completed->value)
                ->count(),
        ];

        return Inertia::render('PickLists/Index', [
            'pickLists' => $pickLists,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing'])
            ->whereDoesntHave('pickList')
            ->with('customer')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('PickLists/Create', [
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items.product')
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($validated['order_id']);

        if (! in_array($order->status->value, ['confirmed', 'processing'], true)) {
            return back()->with('error', 'Only confirmed or processing orders can have pick lists generated.');
        }

        if (PickList::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'This order already has a pick list.');
        }

        try {
            $pickList = $this->generatePickListAction->execute($order);
        } catch (OrderAlreadyPickedException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (! empty($validated['notes'])) {
            $pickList->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('pick-lists.show', $pickList)->with('success', 'Pick list generated successfully');
    }

    public function show(Request $request, PickList $pickList): Response
    {
        $pickList->load([
            'order.customer',
            'warehouse',
            'picker',
            'items' => fn ($q) => $q->with(['product', 'orderItem', 'bin']),
        ]);

        return Inertia::render('PickLists/Show', [
            'pickList' => $pickList,
        ]);
    }

    public function start(Request $request, PickList $pickList): RedirectResponse
    {
        if (! in_array($pickList->status->value, [PickListStatus::Pending->value, PickListStatus::Draft->value], true)) {
            return back()->with('error', 'Pick list cannot be started in its current status.');
        }

        $pickList->update([
            'status' => PickListStatus::InProgress,
            'picker_id' => $request->user()->id,
            'started_at' => now(),
        ]);

        return back()->with('success', 'Pick list started');
    }

    public function complete(Request $request, PickList $pickList): RedirectResponse
    {
        if ($pickList->status->value !== PickListStatus::InProgress->value) {
            return back()->with('error', 'Only in-progress pick lists can be completed.');
        }

        $unpicked = $pickList->items()->where('status', '!=', PickItemStatus::Picked->value)->count();

        if ($unpicked > 0) {
            return back()->with('error', 'Not all items have been picked.');
        }

        $pickList->update([
            'status' => PickListStatus::Completed,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Pick list completed');
    }

    public function update(Request $request, PickList $pickList): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:pick_list_items,id',
            'items.*.quantity_picked' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            foreach ($validated['items'] as $itemData) {
                $item = PickListItem::where('id', $itemData['id'])
                    ->where('pick_list_id', $pickList->id)
                    ->firstOrFail();

                $this->pickItemAction->execute(
                    $item,
                    (float) $itemData['quantity_picked'],
                    $itemData['notes'] ?? null
                );
            }
        } catch (\App\Exceptions\ExceedsPickQuantityException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Picked quantities updated');
    }
}