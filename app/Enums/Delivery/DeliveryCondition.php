<?php

namespace App\Enums\Delivery;

enum DeliveryCondition: string
{
    case Good = 'good';
    case Damaged = 'damaged';
    case WrongItem = 'wrong_item';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Damaged => 'Damaged',
            self::WrongItem => 'Wrong Item',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Good => 'success',
            self::Damaged => 'danger',
            self::WrongItem => 'danger',
        };
    }
}
