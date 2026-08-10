<?php

namespace App\Enums\PickingPacking;

enum PackingStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Packed = 'packed';
    case Verified = 'verified';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InProgress => 'In Progress',
            self::Packed => 'Packed',
            self::Verified => 'Verified',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::InProgress => 'info',
            self::Packed => 'success',
            self::Verified => 'success',
        };
    }
}
