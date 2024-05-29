<?php

namespace App\Enum;

enum CouponStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
        };
    }
}
