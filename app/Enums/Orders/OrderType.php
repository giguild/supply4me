<?php

namespace App\Enums\Orders;

enum OrderType: string
{
    case Sales = 'sales';
    case Return = 'return';
    case Exchange = 'exchange';

    public function label(): string
    {
        return match ($this) {
            self::Sales => 'Sales',
            self::Return => 'Return',
            self::Exchange => 'Exchange',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Sales => 'primary',
            self::Return => 'warning',
            self::Exchange => 'info',
        };
    }
}
