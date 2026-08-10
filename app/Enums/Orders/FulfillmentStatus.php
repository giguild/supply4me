<?php

namespace App\Enums\Orders;

enum FulfillmentStatus: string
{
    case Unfulfilled = 'unfulfilled';
    case Partial = 'partial';
    case Fulfilled = 'fulfilled';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Unfulfilled => 'Unfulfilled',
            self::Partial => 'Partial',
            self::Fulfilled => 'Fulfilled',
            self::Returned => 'Returned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unfulfilled => 'danger',
            self::Partial => 'warning',
            self::Fulfilled => 'success',
            self::Returned => 'info',
        };
    }
}
