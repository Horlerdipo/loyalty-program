<?php

namespace App\Enums;

enum BadgeName: string
{
    case Starter = 'starter';
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';
    case Sapphire = 'sapphire';
    case Ruby = 'ruby';
    case Emerald = 'emerald';
    case Diamond = 'diamond';
    case Legend = 'legend';

    public function requiredCount(): int
    {
        return match ($this) {
            self::Starter => 1,
            self::Bronze => 3,
            self::Silver => 6,
            self::Gold => 9,
            self::Platinum => 12,
            self::Sapphire => 15,
            self::Ruby => 18,
            self::Emerald => 21,
            self::Diamond => 24,
            self::Legend => 27,
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Starter => 1,
            self::Bronze => 2,
            self::Silver => 3,
            self::Gold => 4,
            self::Platinum => 5,
            self::Sapphire => 6,
            self::Ruby => 7,
            self::Emerald => 8,
            self::Diamond => 9,
            self::Legend => 10,
        };
    }
}
