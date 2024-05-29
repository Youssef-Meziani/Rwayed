<?php

namespace App\Enum;

enum TypeVehiculeEnum
{
    public const CAR = 'Car';
//    public const MOTO = 'Moto';
    public const TRUCK = 'Truck';

    public static function getAll(): array
    {
        return [
            self::CAR,
            self::TRUCK,
        ];
    }
}
