<?php

namespace App\Enums\Companies;

enum BranchType: string
{
    case Warehouse = 'warehouse';
    case Office = 'office';
    case Store = 'store';
    case DistributionCenter = 'distribution_center';

    public function label(): string
    {
        return match ($this) {
            self::Warehouse => 'Warehouse',
            self::Office => 'Office',
            self::Store => 'Store',
            self::DistributionCenter => 'Distribution Center',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Warehouse => 'warning',
            self::Office => 'info',
            self::Store => 'success',
            self::DistributionCenter => 'primary',
        };
    }
}
