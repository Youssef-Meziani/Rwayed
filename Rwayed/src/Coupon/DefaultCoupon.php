<?php

declare(strict_types=1);

namespace App\Coupon;

use App\Contract\DiscountedObjectInterface;
use App\Contract\DiscountModelInterface;
use App\Entity\Commande;
use App\Strategy\CouponStrategyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This voucher manager handles the vouchers via Doctrine.
 */
class DefaultCoupon implements CouponInterface
{
    public function __construct(
        private readonly RequestStack $stack,
        private readonly CouponStrategyInterface $strategy,
    ) {
    }

    public function applyVoucher(string $couponCode, Commande $object): DiscountedObjectInterface
    {
        if (false === $this->isAlreadyApplied($couponCode, $object)) {
            // if the voucher is already applied to the current cart, no need to re-apply it again.
            $this->placeNewVoucher($couponCode);
        }

        $voucherInstance = $this->strategy->findVoucherModel($couponCode);

        return $this->getDiscountedCommande($object, $voucherInstance);
    }

    public function isAlreadyApplied(?string $couponCode, Commande $cart): bool
    {
        if (null === $couponCode) {
            return false;
        }

        return $this->stack->getSession()->has('coupon_code');
    }

    public function getVoucherIdentifier(): ?string
    {
        $session = $this->stack->getSession();

        return $session->has('coupon_code') ? $session->get('coupon_code') : null;
    }

    public function placeNewVoucher(string $couponIdentifier): bool
    {
        // if the voucher is already applied to the current cart, no need to re-apply it again.
        $session = $this->stack->getSession();
        $session->set('voucher_code', $couponIdentifier);

        return true;
    }

    public function getDiscountedCommande(Commande $object, ?DiscountModelInterface $voucherInstance): DiscountedObjectInterface
    {
        return new class($object, $voucherInstance) implements DiscountedObjectInterface {
            private Commande $object;
            private ?DiscountModelInterface $voucherInstance;

            public function __construct(Commande $object, ?DiscountModelInterface $voucherInstance)
            {
                $this->object = $object;
                $this->voucherInstance = $voucherInstance;
            }

            public function getTotal(): float
            {
                $total = $this->object->computeTotal();

                if (null === $this->voucherInstance) {
                    return $total;
                }

                return $total * (1 - $this->voucherInstance->getReduction());
            }

            public function getDiscountValue(): float
            {
                if (null === $this->voucherInstance) {
                    return 0.0;
                }
                return $this->object->computeTotal() * $this->voucherInstance->getReduction();
            }

            public function getDiscountRate(): float
            {
                if (null === $this->voucherInstance) {
                    return 0.0;
                }
                return $this->voucherInstance->getPourcentage() ;
            }

            public function getDiscountType(): string
            {
                return $this->voucherInstance->getType();
            }
        };
    }

    public function invalidateCoupon(?string $couponIdentifier): bool
    {
        if (null === $couponIdentifier) {
            return false;
        }

        $session = $this->stack->getSession();
        $session->remove('voucher_code');

        return true;
    }
}
