<?php

namespace App\Enum;

enum PanierStatus: string
{
    case PENDING = 'Pending';
    case SHIPPED = 'Shipped';
    case DELIVERED = 'Delivered';
    case CANCELED = 'Canceled';

    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELED => 'Canceled',
        };
    }
}
