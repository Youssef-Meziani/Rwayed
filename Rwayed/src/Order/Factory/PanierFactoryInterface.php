<?php

namespace App\Order\Factory;

use App\OrderManager\OrderManager;

interface PanierFactoryInterface
{
    public function create(): OrderManager;
}
