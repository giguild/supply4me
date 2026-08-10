<?php

namespace App\Enums\Customers;

enum CustomerType: string
{
    case Retailer = 'retailer';
    case Wholesaler = 'wholesaler';
    case Distributor = 'distributor';
    case Institution = 'institution';
    case Government = 'government';
    case Individual = 'individual';

    public function label(): string
    {
        return match ($this) {
            self::Retailer => 'Retailer',
            self::Wholesaler => 'Wholesaler',
            self::Distributor => 'Distributor',
            self::Institution => 'Institution',
            self::Government => 'Government',
            self::Individual => 'Individual',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Retailer => 'success',
            self::Wholesaler => 'primary',
            self::Distributor => 'info',
            self::Institution => 'warning',
            self::Government => 'danger',
            self::Individual => 'success',
        };
    }
}
