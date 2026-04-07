<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Ordered = 'ordered';
    case Partial = 'partial';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Ordered => 'Dipesan',
            self::Partial => 'Diterima Sebagian',
            self::Received => 'Diterima',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft => 'gray',
            self::Ordered => 'blue',
            self::Partial => 'yellow',
            self::Received => 'green',
            self::Cancelled => 'red',
        };
    }
}
