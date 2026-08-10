<?php

namespace App\Enums\Customers;

enum CustomerStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Blacklisted = 'blacklisted';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Blacklisted => 'Blacklisted',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'secondary',
            self::Suspended => 'warning',
            self::Blacklisted => 'danger',
        };
    }
}
