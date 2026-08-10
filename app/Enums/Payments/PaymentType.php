<?php

namespace App\Enums\Payments;

enum PaymentType: string
{
    case CustomerPayment = 'customer_payment';
    case SupplierPayment = 'supplier_payment';
    case Refund = 'refund';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::CustomerPayment => 'Customer Payment',
            self::SupplierPayment => 'Supplier Payment',
            self::Refund => 'Refund',
            self::Adjustment => 'Adjustment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CustomerPayment => 'success',
            self::SupplierPayment => 'primary',
            self::Refund => 'warning',
            self::Adjustment => 'info',
        };
    }
}
