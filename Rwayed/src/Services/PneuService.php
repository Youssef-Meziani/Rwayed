<?php

namespace App\Services;

use App\Entity\Pneu;
use App\Repository\PneuRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PneuService
{
    private $pneuRepository;

    public function __construct(PneuRepository $pneuRepository)
    {
        $this->pneuRepository = $pneuRepository;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isPneuHot($pneuId): bool
    {
        $totalOrders = $this->pneuRepository->countOrdersForPneu($pneuId);
        return $totalOrders > 1;
    }

    public function isPneuNew(Pneu $pneu): bool  // Correct type hint
    {
        $now = new \DateTime();
        $interval = $now->diff($pneu->getDateAjout());
            return $interval->days <= 30;
    }

    public function isPneuOnSale(Pneu $pneu): bool  // Correct type hint
    {
        return $pneu->getQuantiteStock() === 0;
    }
}
