<?php

namespace App\Enums\Inventory;

enum TransferItemCondition: string
{
    case Good = 'good';
    case Damaged = 'damaged';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Damaged => 'Damaged',
            self::Expired => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Good => 'success',
            self::Damaged => 'orange',
            self::Expired => 'danger',
        };
    }
}