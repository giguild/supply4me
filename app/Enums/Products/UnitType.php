<?php

namespace App\Enums\Products;

enum UnitType: string
{
    case Piece = 'piece';
    case Box = 'box';
    case Kg = 'kg';
    case Litre = 'litre';
    case Metre = 'metre';
    case Dozen = 'dozen';
    case Pack = 'pack';

    public function label(): string
    {
        return match ($this) {
            self::Piece => 'Piece',
            self::Box => 'Box',
            self::Kg => 'Kg',
            self::Litre => 'Litre',
            self::Metre => 'Metre',
            self::Dozen => 'Dozen',
            self::Pack => 'Pack',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Piece => 'primary',
            self::Box => 'info',
            self::Kg => 'success',
            self::Litre => 'warning',
            self::Metre => 'danger',
            self::Dozen => 'secondary',
            self::Pack => 'dark',
        };
    }
}
