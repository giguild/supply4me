<?php

namespace App\Enums\Delivery;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case PartialDelivery = 'partial_delivery';
    case FailedAttempt = 'failed_attempt';
    case Returned = 'returned';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Assigned => 'Assigned',
            self::OutForDelivery => 'Out for Delivery',
            self::Delivered => 'Delivered',
            self::PartialDelivery => 'Partial Delivery',
            self::FailedAttempt => 'Failed Attempt',
            self::Returned => 'Returned',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'secondary',
            self::Assigned => 'info',
            self::OutForDelivery => 'warning',
            self::Delivered => 'success',
            self::PartialDelivery => 'warning',
            self::FailedAttempt => 'danger',
            self::Returned => 'danger',
            self::Cancelled => 'danger',
        };
    }
}
