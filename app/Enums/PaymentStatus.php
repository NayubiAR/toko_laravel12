<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Pending = 'pending';
    case Partial = 'partial';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Paid => 'Lunas',
            self::Pending => 'Menunggu',
            self::Partial => 'Sebagian',
            self::Failed => 'Gagal',
            self::Refunded => 'Refund',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Paid => 'green',
            self::Pending => 'yellow',
            self::Partial => 'blue',
            self::Failed => 'red',
            self::Refunded => 'gray',
        };
    }
}
