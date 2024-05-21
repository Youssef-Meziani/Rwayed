<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Contract\DiscountModelInterface;

interface CouponStrategyInterface
{
    public function findVoucherModel(?string $voucherIdentifier): ?DiscountModelInterface;
    public function loadVoucher(?string $voucherIdentifier): mixed;
}
