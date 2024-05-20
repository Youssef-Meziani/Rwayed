<?php

namespace App\Coupon;

use App\Entity\Commande;
use App\Contract\DiscountedObjectInterface;
use App\Contract\DiscountModelInterface;

interface CouponInterface
{
    
    public function placeNewVoucher(string $couponIdentifier): bool;

    
    public function applyVoucher(string $couponCode, Commande $object): DiscountedObjectInterface;

    
    public function isAlreadyApplied(?string $couponCode, Commande $cart): bool;

    
    public function getVoucherIdentifier(): ?string;

    public function getDiscountedCommande(Commande $object, ?DiscountModelInterface $voucherInstance): DiscountedObjectInterface;

    
    public function invalidateCoupon(?string $couponIdentifier): bool;
}
