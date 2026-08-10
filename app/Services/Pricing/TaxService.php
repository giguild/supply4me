<?php

namespace App\Services\Pricing;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\ValueObjects\Money;

class TaxService
{
    /**
     * Calculate tax for a single order item.
     */
    public function calculateItemTax(OrderItem $item): Money
    {
        if ($item->tax_amount > 0) {
            return Money::from((float) $item->tax_amount, 'USD');
        }

        $product = $item->product;

        if (!$product) {
            return Money::zero('USD');
        }

        $unitPrice = Money::from((float) $item->unit_price, 'USD');
        $quantity = (float) $item->quantity;
        $lineTotal = $unitPrice->multiply($quantity);

        $taxRate = (float) $product->tax_rate;

        if ($item->discount_percentage > 0) {
            $discountAmount = $lineTotal->multiply($item->discount_percentage / 100);
            $lineTotal = $lineTotal->subtract($discountAmount);
        }

        return $lineTotal->multiply($taxRate / 100);
    }

    /**
     * Calculate total tax for an entire order.
     */
    public function calculateOrderTax(Order $order): Money
    {
        $totalTax = Money::zero($order->currency_code ?? 'USD');

        foreach ($order->items as $item) {
            $itemTax = $this->calculateItemTax($item);
            $totalTax = $totalTax->add($itemTax);
        }

        return $totalTax;
    }

    /**
     * Get the tax rate for a product considering customer-specific rates.
     */
    public function getTaxRate(Product $product, ?Customer $customer = null): float
    {
        return (float) $product->tax_rate;
    }
}
