<?php

namespace App\Enums\Customers;

enum CreditStatus: string
{
    case Good = 'good';
    case Overdue = 'overdue';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Overdue => 'Overdue',
            self::Blocked => 'Blocked',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Good => 'success',
            self::Overdue => 'warning',
            self::Blocked => 'danger',
        };
    }
}
