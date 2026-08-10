<?php

namespace App\Enums\Inventory;

enum AdjustmentType: string
{
    case CycleCount = 'cycle_count';
    case PhysicalCount = 'physical_count';
    case Damage = 'damage';
    case Expiry = 'expiry';
    case Shrinkage = 'shrinkage';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CycleCount => 'Cycle Count',
            self::PhysicalCount => 'Physical Count',
            self::Damage => 'Damage',
            self::Expiry => 'Expiry',
            self::Shrinkage => 'Shrinkage',
            self::Other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CycleCount => 'info',
            self::PhysicalCount => 'primary',
            self::Damage => 'danger',
            self::Expiry => 'warning',
            self::Shrinkage => 'danger',
            self::Other => 'secondary',
        };
    }
}
