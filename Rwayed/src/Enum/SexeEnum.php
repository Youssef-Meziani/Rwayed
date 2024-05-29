<?php

namespace App\Enum;

enum SexeEnum: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';

    public function label(): string
    {
        return match($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        };
    }
}
