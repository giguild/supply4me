<?php

namespace App\Services\Order;

use App\Models\Orders\Order;
use App\Services\Pricing\DiscountService;
use App\Services\Pricing\PricingService;
use App\Services\Pricing\TaxService;
use App\ValueObjects\Money;

class OrderCalculationService
{
    public function __construct(
        private readonly PricingService $pricingService,
        private readonly DiscountService $discountService,
        private readonly TaxService $taxService,
    ) {}

    /**
     * Calculate order subtotal from all items.
     */
    public function calculateSubtotal(Order $order): Money
    {
        $subtotal = Money::zero($order->currency_code ?? 'USD');

        foreach ($order->items as $item) {
            $lineTotal = $this->pricingService->calculateLineTotal($item);
            $subtotal = $subtotal->add($lineTotal);
        }

        return $subtotal;
    }

    /**
     * Calculate total discount for the order.
     */
    public function calculateDiscount(Order $order): Money
    {
        return $this->discountService->calculateOrderDiscount($order);
    }

    /**
     * Calculate total tax for the order.
     */
    public function calculateTax(Order $order): Money
    {
        return $this->taxService->calculateOrderTax($order);
    }

    /**
     * Calculate shipping cost for the order.
     */
    public function calculateShipping(Order $order): Money
    {
        return Money::from((float) $order->shipping_amount, $order->currency_code ?? 'USD');
    }

    /**
     * Calculate the grand total for the order.
     */
    public function calculateTotal(Order $order): Money
    {
        $subtotal = $this->calculateSubtotal($order);
        $discount = $this->calculateDiscount($order);
        $tax = $this->calculateTax($order);
        $shipping = $this->calculateShipping($order);

        $total = $subtotal->subtract($discount)->add($tax)->add($shipping);

        return $total;
    }

    /**
     * Recalculate and update all order totals.
     */
    public function recalculateOrder(Order $order): void
    {
        $subtotal = $this->calculateSubtotal($order);
        $discount = $this->calculateDiscount($order);
        $tax = $this->calculateTax($order);
        $shipping = $this->calculateShipping($order);
        $total = $subtotal->subtract($discount)->add($tax)->add($shipping);

        $order->update([
            'subtotal' => $subtotal->getAmount(),
            'discount_amount' => $discount->getAmount(),
            'tax_amount' => $tax->getAmount(),
            'shipping_amount' => $shipping->getAmount(),
            'total_amount' => $total->getAmount(),
        ]);
    }
}
