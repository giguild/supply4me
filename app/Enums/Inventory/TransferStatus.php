<?php

namespace App\Enums\Inventory;

enum TransferStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case InTransit = 'in_transit';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingApproval => 'Pending Approval',
            self::Approved => 'Approved',
            self::InTransit => 'In Transit',
            self::Received => 'Received',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::PendingApproval => 'warning',
            self::Approved => 'success',
            self::InTransit => 'info',
            self::Received => 'success',
            self::Cancelled => 'danger',
        };
    }
}
