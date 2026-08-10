<?php

namespace App\Enums\Payments;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Check = 'check';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case MobileMoney = 'mobile_money';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::BankTransfer => 'Bank Transfer',
            self::Check => 'Check',
            self::CreditCard => 'Credit Card',
            self::DebitCard => 'Debit Card',
            self::MobileMoney => 'Mobile Money',
            self::Other => 'Other',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Cash => 'success',
            self::BankTransfer => 'primary',
            self::Check => 'info',
            self::CreditCard => 'warning',
            self::DebitCard => 'warning',
            self::MobileMoney => 'info',
            self::Other => 'secondary',
        };
    }
}
