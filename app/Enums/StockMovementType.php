<?php

namespace App\Enums;

enum StockMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjustment = 'adjustment';
    case Transfer = 'transfer';
    case Return = 'return';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match($this) {
            self::In => 'Barang Masuk',
            self::Out => 'Barang Keluar',
            self::Adjustment => 'Penyesuaian',
            self::Transfer => 'Transfer',
            self::Return => 'Retur',
            self::Damaged => 'Rusak/Expired',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::In, self::Return]);
    }
}
