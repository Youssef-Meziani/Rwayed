<?php

declare(strict_types=1);

namespace App\Processor;

use App\Order\Storage\OrderStorageInterface;
use App\Order\Persister\CartPersisterInterface;
use App\Coupon\ReductionLoader;
use App\Entity\Commande;
use App\Strategy\CouponStrategyInterface;
use App\Coupon\CouponInterface;

final class CartProcessor
{
    public function __construct(
        private readonly ReductionLoader $reductionLoader,
        private readonly CouponStrategyInterface $strategy,
        private readonly CouponInterface $couponManager,
        private readonly OrderStorageInterface $orderStorage,
        private readonly CartPersisterInterface $commandePersister,
    ) {
    }

    /**
     * Processes the cart.
     */
    public function process(Commande $instance, ?string $couponIdentifier): void
    {
        if (null !== $couponIdentifier) {
            $this->reductionLoader->loadDiscount($instance, $couponIdentifier, $this->strategy);
            $instance = $this->doUpdateTotal($instance, $couponIdentifier);

            $this->doCleaning();
        } else {

            $instance->setTotal($instance->computeTotal());
        }
        $this->commandePersister->persist($instance);
    }

    /**
     * Recomputes the totals based on the strategy used.
     */
    private function doUpdateTotal(Commande $commande, string $couponIdentifier): Commande
    {
        $voucherModel = $this->strategy->findVoucherModel($couponIdentifier);

        $discountedCart = $this->couponManager->getDiscountedCommande($commande, $voucherModel);

        $commande->setTotal($discountedCart->getTotal());

        return $commande;
    }

    /**
     * Internally clears the cart from the storage and invalidates the voucher.
     */
    private function doCleaning(): void
    {
        $this->orderStorage->supprimerPanier($this->orderStorage->recuprerPanier());
    }
}
