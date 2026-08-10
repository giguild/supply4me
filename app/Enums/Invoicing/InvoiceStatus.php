<?php

namespace App\Enums\Invoicing;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Paid = 'paid';
    case Partial = 'partial';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
    case Void = 'void';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::Paid => 'Paid',
            self::Partial => 'Partial',
            self::Overdue => 'Overdue',
            self::Cancelled => 'Cancelled',
            self::Void => 'Void',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Pending => 'warning',
            self::Sent => 'info',
            self::Viewed => 'info',
            self::Paid => 'success',
            self::Partial => 'warning',
            self::Overdue => 'danger',
            self::Cancelled => 'danger',
            self::Void => 'dark',
        };
    }
}
