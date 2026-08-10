<?php

namespace App\Services\Pricing;

use App\Models\Catalog\Promotion;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\ValueObjects\Money;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * Calculate total discount for an entire order.
     */
    public function calculateOrderDiscount(Order $order): Money
    {
        $totalDiscount = Money::zero($order->currency_code ?? 'USD');

        foreach ($order->items as $item) {
            $itemDiscount = $this->calculateItemDiscount($item);
            $totalDiscount = $totalDiscount->add($itemDiscount);
        }

        return $totalDiscount;
    }

    /**
     * Calculate discount for a single order item.
     */
    public function calculateItemDiscount(OrderItem $item): Money
    {
        $unitPrice = Money::from((float) $item->unit_price, 'USD');
        $quantity = (float) $item->quantity;
        $lineTotal = $unitPrice->multiply($quantity);

        if ($item->discount_percentage > 0) {
            return $lineTotal->multiply($item->discount_percentage / 100);
        }

        return Money::zero('USD');
    }

    /**
     * Apply a promotion to an order and return the discount amount.
     */
    public function applyPromotion(Promotion $promotion, Order $order): Money
    {
        if (!$this->isPromotionValid($promotion, $order)) {
            return Money::zero($order->currency_code ?? 'USD');
        }

        $orderSubtotal = Money::zero($order->currency_code ?? 'USD');

        foreach ($order->items as $item) {
            $unitPrice = Money::from((float) $item->unit_price, 'USD');
            $lineTotal = $unitPrice->multiply((float) $item->quantity);
            $orderSubtotal = $orderSubtotal->add($lineTotal);
        }

        return $this->calculatePromotionDiscount($promotion, $orderSubtotal);
    }

    /**
     * Check if a promotion is valid for the given order.
     */
    private function isPromotionValid(Promotion $promotion, Order $order): bool
    {
        $now = now();

        if ($promotion->valid_from && $promotion->valid_from->isAfter($now)) {
            return false;
        }

        if ($promotion->valid_until && $promotion->valid_until->isBefore($now)) {
            return false;
        }

        if ($promotion->usage_limit && $promotion->usage_count >= $promotion->usage_limit) {
            return false;
        }

        $orderSubtotal = Money::zero($order->currency_code ?? 'USD');
        foreach ($order->items as $item) {
            $unitPrice = Money::from((float) $item->unit_price, 'USD');
            $orderSubtotal = $orderSubtotal->add($unitPrice->multiply((float) $item->quantity));
        }

        if ($promotion->minimum_amount && $orderSubtotal->getAmount() < (float) $promotion->minimum_amount) {
            return false;
        }

        if ($promotion->minimum_quantity) {
            $totalQuantity = 0;
            foreach ($order->items as $item) {
                $totalQuantity += (float) $item->quantity;
            }
            if ($totalQuantity < (float) $promotion->minimum_quantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the discount amount from a promotion.
     */
    private function calculatePromotionDiscount(Promotion $promotion, Money $orderSubtotal): Money
    {
        $discount = $orderSubtotal->multiply((float) $promotion->value / 100);

        if ($promotion->maximum_discount) {
            $maxDiscount = Money::from((float) $promotion->maximum_discount, $orderSubtotal->getCurrency());
            if ($discount->getAmount() > $maxDiscount->getAmount()) {
                return $maxDiscount;
            }
        }

        return $discount;
    }
}
