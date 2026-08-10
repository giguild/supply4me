<?php

namespace App\Enums\PickingPacking;

enum PickItemStatus: string
{
    case Pending = 'pending';
    case Picking = 'picking';
    case Picked = 'picked';
    case Short = 'short';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Picking => 'Picking',
            self::Picked => 'Picked',
            self::Short => 'Short',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Picking => 'info',
            self::Picked => 'success',
            self::Short => 'danger',
        };
    }
}
