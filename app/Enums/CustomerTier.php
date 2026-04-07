<?php

namespace App\Enums;

enum CustomerTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';

    public function label(): string
    {
        return match($this) {
            self::Bronze => 'Bronze',
            self::Silver => 'Silver',
            self::Gold => 'Gold',
            self::Platinum => 'Platinum',
        };
    }

    public function discountPercent(): float
    {
        return match($this) {
            self::Bronze => 0,
            self::Silver => 2,
            self::Gold => 5,
            self::Platinum => 10,
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Bronze => '#CD7F32',
            self::Silver => '#C0C0C0',
            self::Gold => '#FFD700',
            self::Platinum => '#E5E4E2',
        };
    }
}
