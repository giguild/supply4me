<?php

namespace App\Enums\Inventory;

enum StockStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Quarantine = 'quarantine';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Quarantine => 'Quarantine',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Quarantine => 'warning',
        };
    }
}
