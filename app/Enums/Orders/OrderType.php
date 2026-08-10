<?php

namespace App\Enums\Orders;

enum OrderType: string
{
    case Standard = 'standard';
    case Repeat = 'repeat';
    case Standing = 'standing';
    case Sample = 'sample';
    case Exchange = 'exchange';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Repeat => 'Repeat',
            self::Standing => 'Standing',
            self::Sample => 'Sample',
            self::Exchange => 'Exchange',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Standard => 'primary',
            self::Repeat => 'info',
            self::Standing => 'success',
            self::Sample => 'warning',
            self::Exchange => 'danger',
        };
    }
}
