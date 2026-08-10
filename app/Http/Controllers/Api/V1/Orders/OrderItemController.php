<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Actions\Orders\AddOrderItemAction;
use App\Actions\Orders\UpdateOrderItemAction;
use App\Actions\Orders\RemoveOrderItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\AddOrderItemRequest;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Resources\Orders\OrderItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function __construct(
        protected AddOrderItemAction $addOrderItemAction,
        protected UpdateOrderItemAction $updateOrderItemAction,
        protected RemoveOrderItemAction $removeOrderItemAction
    ) {}

    public function index(Order $order): JsonResponse
    {
        $items = $order->items()->with('product')->get();

        return $this->success(OrderItemResource::collection($items));
    }

    public function store(AddOrderItemRequest $request, Order $order): JsonResponse
    {
        $item = $this->addOrderItemAction->execute($order, $request->validated());

        return $this->created(
            new OrderItemResource($item->load('product')),
            'Item added to order successfully'
        );
    }

    public function show(Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            abort(403, 'Item does not belong to this order');
        }

        return $this->success(new OrderItemResource($item->load('product')));
    }

    public function update(Request $request, Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            abort(403, 'Item does not belong to this order');
        }

        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = $this->updateOrderItemAction->execute($item, $validated);

        return $this->success(
            new OrderItemResource($item->fresh()->load('product')),
            'Item updated successfully'
        );
    }

    public function destroy(Order $order, OrderItem $item): JsonResponse
    {
        if ($item->order_id !== $order->id) {
            abort(403, 'Item does not belong to this order');
        }

        $this->removeOrderItemAction->execute($item);

        return $this->noContent('Item removed from order successfully');
    }
}
