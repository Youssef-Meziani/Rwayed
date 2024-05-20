<?php

namespace App\Enums;

enum PanierStatus: string
{
    case PENDING = 'Pending';
    case SHIPPED = 'Shipped';
    case DELIVERED = 'Delivered';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
        };
    }
}
