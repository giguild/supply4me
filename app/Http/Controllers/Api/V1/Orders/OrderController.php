<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\ConfirmOrderAction;
use App\Actions\Orders\CancelOrderAction;
use App\Actions\Orders\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\StoreOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Resources\Orders\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected CreateOrderAction $createOrderAction,
        protected ConfirmOrderAction $confirmOrderAction,
        protected CancelOrderAction $cancelOrderAction,
        protected PlaceOrderAction $placeOrderAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['customer', 'items'])
            ->when($request->search, fn ($q, $s) => $q->where('order_number', 'like', "%{$s}%"))
            ->when($request->customer_id, fn ($q, $c) => $q->where('customer_id', $c))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('created_at', '<=', $d))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->paginated($orders, OrderResource::collection($orders->items()));
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->createOrderAction->execute($request->validated());

        return $this->created(
            new OrderResource($order->load(['customer', 'items.product'])),
            'Order created successfully'
        );
    }

    public function show(Order $order): JsonResponse
    {
        return $this->success(
            new OrderResource($order->load(['customer', 'items.product', 'statusHistory', 'payments']))
        );
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();

        if (!empty($validated['shipping_address'])) {
            $order->update(['shipping_address' => $validated['shipping_address']]);
            unset($validated['shipping_address']);
        }

        if (!empty($validated['notes'])) {
            $order->update(['notes' => $validated['notes']]);
        }

        if (!empty($validated['expected_delivery_date'])) {
            $order->update(['expected_delivery_date' => $validated['expected_delivery_date']]);
        }

        return $this->success(
            new OrderResource($order->fresh()->load(['customer', 'items.product'])),
            'Order updated successfully'
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        if (!in_array($order->status, ['draft', 'pending'])) {
            return $this->error('Only draft or pending orders can be deleted', 422);
        }

        $order->items()->delete();
        $order->delete();

        return $this->noContent('Order deleted successfully');
    }

    public function confirm(Order $order): JsonResponse
    {
        $order = $this->confirmOrderAction->execute($order);

        return $this->success(
            new OrderResource($order->fresh()),
            'Order confirmed successfully'
        );
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = $this->cancelOrderAction->execute($order, $validated['reason']);

        return $this->success(
            new OrderResource($order->fresh()),
            'Order cancelled successfully'
        );
    }

    public function place(Order $order): JsonResponse
    {
        $order = $this->placeOrderAction->execute($order);

        return $this->success(
            new OrderResource($order->fresh()),
            'Order placed successfully'
        );
    }

    public function items(Order $order): JsonResponse
    {
        return $this->success($order->items()->with('product')->get());
    }

    public function statusHistory(Order $order): JsonResponse
    {
        return $this->success($order->statusHistory()->latest()->get());
    }
}
