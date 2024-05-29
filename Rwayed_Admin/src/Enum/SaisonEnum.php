<?php

namespace App\Enum;

enum  SaisonEnum
{
    public const SPRING = 'Spring';
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
