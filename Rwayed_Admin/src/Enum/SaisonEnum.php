<?php

namespace App\Enum;

final class SaisonEnum
{
    public const SPRING = 'spring';
    public const SUMMER = 'Summer';
    public const AUTUMN = 'Autumn';
    public const WINTER = 'Winter';
    public const ALL_SEASON = 'All Season';

    public static function getAll(): array
    {
        return [
            self::SPRING,
            self::SUMMER,
            self::AUTUMN,
            self::WINTER,
            self::ALL_SEASON,
        ];
    }
}
