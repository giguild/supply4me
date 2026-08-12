<?php

namespace App\Enums\Payments;

enum PaymentType: string
{
    case Incoming = 'incoming';
    case Outgoing = 'outgoing';

    public function label(): string
    {
        return match ($this) {
            self::Incoming => 'Incoming',
            self::Outgoing => 'Outgoing',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Incoming => 'success',
            self::Outgoing => 'primary',
        };
    }
}
