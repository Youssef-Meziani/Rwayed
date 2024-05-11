<?php

namespace App\Order\Factory;

use App\Order\Storage\OrderSessionStorage;
use App\OrderManager\OrderManager;

class PanierFactory implements PanierFactoryInterface
{
    public function __construct(private OrderSessionStorage $storage)
    {
    }

    public function create(): OrderManager
    {
        return $this->storage->recuprerPanier();
    }
}
