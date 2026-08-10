<?php

namespace App\Enums\Receiving;

enum ItemCondition: string
{
    case Good = 'good';
    case Damaged = 'damaged';
    case Expired = 'expired';
    case WrongItem = 'wrong_item';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Damaged => 'Damaged',
            self::Expired => 'Expired',
            self::WrongItem => 'Wrong Item',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Good => 'success',
            self::Damaged => 'danger',
            self::Expired => 'warning',
            self::WrongItem => 'danger',
        };
    }
}
