<?php

namespace App\Enums\Invoicing;

enum InvoiceType: string
{
    case Sales = 'sales';
    case CreditNote = 'credit_note';
    case DebitNote = 'debit_note';
    case Proforma = 'proforma';

    public function label(): string
    {
        return match ($this) {
            self::Sales => 'Sales',
            self::CreditNote => 'Credit Note',
            self::DebitNote => 'Debit Note',
            self::Proforma => 'Proforma',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Sales => 'primary',
            self::CreditNote => 'success',
            self::DebitNote => 'warning',
            self::Proforma => 'info',
        };
    }
}
