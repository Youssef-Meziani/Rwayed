<?php

namespace App\Enum;

class TypeVehiculeEnum
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
