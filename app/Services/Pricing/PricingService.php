<?php

namespace App\Services\Pricing;

use App\Models\Catalog\PriceList;
use App\Models\Catalog\PriceListItem;
use App\Models\Orders\OrderItem;
use App\Models\Products\Product;
use App\ValueObjects\Money;
use App\ValueObjects\Quantity;
use Illuminate\Support\Collection;

class PricingService
{
    public function __construct(
        private readonly DiscountService $discountService,
        private readonly TaxService $taxService,
    ) {}

    /**
     * Calculate product price based on price list, quantity breaks, and promotions.
     */
    public function getPrice(Product $product, ?PriceList $priceList, Quantity $quantity): Money
    {
        $currency = $priceList?->currency_code ?? $product->selling_price ? 'USD' : 'USD';

        $basePrice = $this->getBasePrice($product, $priceList, $quantity);

        return Money::from($basePrice, $currency);
    }

    /**
     * Calculate line total for an order item including quantity, discounts, and tax.
     */
    public function calculateLineTotal(OrderItem $item): Money
    {
        $unitPrice = Money::from((float) $item->unit_price, 'USD');
        $quantity = Quantity::from((float) $item->quantity);

        $lineSubtotal = $unitPrice->multiply($quantity->getValue());

        if ($item->discount_percentage > 0) {
            $lineSubtotal = $this->applyDiscount($lineSubtotal, (float) $item->discount_percentage);
        }

        return $lineSubtotal;
    }

    /**
     * Apply a percentage discount to a monetary amount.
     */
    public function applyDiscount(Money $amount, float $discountPercentage): Money
    {
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new \InvalidArgumentException("Discount percentage must be between 0 and 100. Got: {$discountPercentage}");
        }

        $discountAmount = $amount->multiply($discountPercentage / 100);

        return $amount->subtract($discountAmount);
    }

    /**
     * Get the base price for a product considering the price list and quantity breaks.
     */
    private function getBasePrice(Product $product, ?PriceList $priceList, Quantity $quantity): float
    {
        if ($priceList) {
            $priceListItem = $this->getPriceListItem($product, $priceList, $quantity);

            if ($priceListItem) {
                return (float) $priceListItem->price;
            }
        }

        return (float) $product->selling_price;
    }

    /**
     * Find the matching price list item for a product and quantity.
     */
    private function getPriceListItem(Product $product, PriceList $priceList, Quantity $quantity): ?PriceListItem
    {
        $now = now();

        return PriceListItem::where('price_list_id', $priceList->id)
            ->where('product_id', $product->id)
            ->where('minimum_quantity', '<=', $quantity->getValue())
            ->where(function ($query) use ($now) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $now);
            })
            ->orderByDesc('minimum_quantity')
            ->first();
    }
}
