<?php

namespace App\Enums\Inventory;

enum MovementType: string
{
    case Receipt = 'receipt';
    case Sale = 'sale';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';
    case Return = 'return';
    case Damage = 'damage';
    case Count = 'count';
    case Reservation = 'reservation';
    case Release = 'release';

    public function label(): string
    {
        return match ($this) {
            self::Receipt => 'Receipt',
            self::Sale => 'Sale',
            self::Transfer => 'Transfer',
            self::Adjustment => 'Adjustment',
            self::Return => 'Return',
            self::Damage => 'Damage',
            self::Count => 'Count',
            self::Reservation => 'Reservation',
            self::Release => 'Release',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Receipt => 'success',
            self::Sale => 'primary',
            self::Transfer => 'info',
            self::Adjustment => 'warning',
            self::Return => 'danger',
            self::Damage => 'danger',
            self::Count => 'secondary',
            self::Reservation => 'info',
            self::Release => 'success',
        };
    }
}
