<?php

namespace App\Enums\Products;

enum ProductType: string
{
    case Standard = 'standard';
    case Variant = 'variant';
    case Bundle = 'bundle';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Variant => 'Variant',
            self::Bundle => 'Bundle',
            self::Service => 'Service',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Standard => 'primary',
            self::Variant => 'info',
            self::Bundle => 'success',
            self::Service => 'warning',
        };
    }
}
