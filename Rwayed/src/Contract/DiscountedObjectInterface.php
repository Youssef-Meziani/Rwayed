<?php

namespace App\Contract;

interface DiscountedObjectInterface
{
    public function getTotal(): float;

    public function getDiscountValue(): float;

    public function getDiscountRate(): float;

    public function getDiscountType():string;
}
