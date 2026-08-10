<?php

namespace App\Enums\Shipping;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Ready = 'ready';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Ready => 'Ready',
            self::PickedUp => 'Picked Up',
            self::InTransit => 'In Transit',
            self::OutForDelivery => 'Out for Delivery',
            self::Delivered => 'Delivered',
            self::Failed => 'Failed',
            self::Returned => 'Returned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Ready => 'info',
            self::PickedUp => 'info',
            self::InTransit => 'primary',
            self::OutForDelivery => 'warning',
            self::Delivered => 'success',
            self::Failed => 'danger',
            self::Returned => 'danger',
        };
    }
}
