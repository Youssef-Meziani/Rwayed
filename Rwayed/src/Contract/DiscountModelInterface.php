<?php

namespace App\Contract;


interface DiscountModelInterface
{
    public function getReduction(): float;
    public function getPourcentage(): float;
    public function getType(): string;
}
