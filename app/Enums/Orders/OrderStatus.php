<?php

namespace App\Enums\Orders;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Picking = 'picking';
    case Packing = 'packing';
    case ReadyToShip = 'ready_to_ship';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Picking => 'Picking',
            self::Packing => 'Packing',
            self::ReadyToShip => 'Ready to Ship',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Completed => 'Completed',
            self::OnHold => 'On Hold',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Processing => 'primary',
            self::Picking => 'info',
            self::Packing => 'info',
            self::ReadyToShip => 'success',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::Completed => 'success',
            self::OnHold => 'danger',
            self::Cancelled => 'danger',
        };
    }
}
