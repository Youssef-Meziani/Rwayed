<?php

namespace App\Enums;

enum PanierStatus: string
{
    case placed = 'placed';
    case cancelled = 'canceled';
}