<?php

declare(strict_types=1);
namespace App\Strategy\CouponStrategy;

use App\Contract\DiscountModelInterface;
use App\Entity\CodePromo;
use App\Repository\CodePromoRepository;
use App\Strategy\CouponStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineStrategy implements CouponStrategyInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
    )
    {}

    public function findVoucherModel(?string $voucherIdentifier): ?DiscountModelInterface
    {
        if (null === $voucherIdentifier) {
            return null;
        }

        $adaptedVoucher = $this->loadVoucher($voucherIdentifier);

        if (null === $adaptedVoucher) {
            return null;
        }

        return new class($adaptedVoucher) implements DiscountModelInterface
        {
            private CodePromo $adaptedObject;

            public function __construct(CodePromo $adaptedObject)
            {
                $this->adaptedObject = $adaptedObject;
            }

            public function getType(): string
            {
                return 'coupon';
            }

            public function getReduction(): float
            {
                return 0.01 * $this->getPourcentage();
            }

            public function getPourcentage(): float
            {
                return $this->adaptedObject->getPourcentage();
            }
        };
    }

    public function loadVoucher(?string $voucherIdentifier): mixed
    {
        if (null === $voucherIdentifier) {
            return null;
        }

        /** @var CodePromoRepository $voucherRepository */
        $voucherRepository = $this->manager->getRepository(CodePromo::class);

        return $voucherRepository->findOneBy(['code' => $voucherIdentifier]);
    }
}
