<?php

namespace App\Enum;

final class SaisonEnum
{
    public const SPRING = 'spring';
    public const SUMMER = 'Summer';
    public const AUTUMN = 'Autumn';
    public const WINTER = 'Winter';

    public static function getAll(): array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
        ];
    }
}
