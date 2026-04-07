<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Qris = 'qris';
    case BankTransfer = 'bank_transfer';
    case DebitCard = 'debit_card';
    case CreditCard = 'credit_card';
    case Split = 'split';

    public function label(): string
    {
        return match($this) {
            self::Cash => 'Cash',
            self::Qris => 'QRIS',
            self::BankTransfer => 'Transfer Bank',
            self::DebitCard => 'Kartu Debit',
            self::CreditCard => 'Kartu Kredit',
            self::Split => 'Split Payment',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Cash => 'banknotes',
            self::Qris => 'qr-code',
            self::BankTransfer => 'building-library',
            self::DebitCard => 'credit-card',
            self::CreditCard => 'credit-card',
            self::Split => 'arrows-right-left',
        };
    }
}
