<?php

declare(strict_types=1);

namespace App\Coupon;

use App\Entity\Commande;
use App\Strategy\CouponStrategyInterface;

class ReductionLoader
{
    public function loadDiscount(Commande $instance, string $couponIdentifier, CouponStrategyInterface $strategy): Commande
    {
        $discount = $strategy->loadVoucher($couponIdentifier);
        return $instance->setCodePromo($discount);
    }
}
