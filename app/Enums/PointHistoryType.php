<?php

namespace App\Enums;

enum PointHistoryType: string
{
    case Earned = 'earned';
    case Redeemed = 'redeemed';
    case Adjusted = 'adjusted';
    case Expired = 'expired';
    case Bonus = 'bonus';

    public function label(): string
    {
        return match($this) {
            self::Earned => 'Poin Didapat',
            self::Redeemed => 'Poin Ditukar',
            self::Adjusted => 'Penyesuaian',
            self::Expired => 'Kadaluarsa',
            self::Bonus => 'Bonus',
        };
    }
}
