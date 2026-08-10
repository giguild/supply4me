<?php

namespace App\Actions\Orders;

use App\Enums\Orders\FulfillmentStatus;
use App\Enums\Orders\OrderStatus;
use App\Enums\Orders\PaymentStatus;
use App\Events\Orders\OrderCreated;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function execute(array $data, \App\Models\Core\User $createdBy): Order
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $subtotal = 0;
            $taxAmount = 0;

            if (! empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    $itemDiscount = ($item['discount_percentage'] ?? 0) > 0
                        ? $itemTotal * ($item['discount_percentage'] / 100)
                        : 0;
                    $itemTax = ($itemTotal - $itemDiscount) * (($item['tax_rate'] ?? 0) / 100);
                    $subtotal += $itemTotal - $itemDiscount;
                    $taxAmount += $itemTax;
                }
            }

            $discountAmount = $data['discount_amount'] ?? 0;
            $shippingAmount = $data['shipping_amount'] ?? 0;
            $totalAmount = $subtotal - $discountAmount + $taxAmount + $shippingAmount;

            $order = Order::create([
                'company_id' => $data['company_id'],
                'customer_id' => $data['customer_id'],
                'branch_id' => $data['branch_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'price_list_id' => $data['price_list_id'] ?? null,
                'order_type' => $data['order_type'] ?? 'sales',
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'fulfillment_status' => FulfillmentStatus::Unfulfilled,
                'priority' => $data['priority'] ?? 'normal',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $totalAmount,
                'currency_code' => $data['currency_code'] ?? 'USD',
                'payment_terms_days' => $data['payment_terms_days'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'shipping_address_id' => $data['shipping_address_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);

            if (! empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                    $itemDiscount = ($itemData['discount_percentage'] ?? 0) > 0
                        ? $itemTotal * ($itemData['discount_percentage'] / 100)
                        : 0;
                    $itemTax = ($itemTotal - $itemDiscount) * (($itemData['tax_rate'] ?? 0) / 100);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'variant_id' => $itemData['variant_id'] ?? null,
                        'unit_id' => $itemData['unit_id'] ?? null,
                        'sku' => $itemData['sku'] ?? null,
                        'name' => $itemData['name'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'discount_percentage' => $itemData['discount_percentage'] ?? 0,
                        'tax_amount' => $itemTax,
                        'total_amount' => $itemTotal - $itemDiscount + $itemTax,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            event(new OrderCreated($order, $createdBy));

            return $order;
        });
    }
}
