<?php

namespace App\Twig;

use App\Entity\Pneu;
use App\Services\PneuService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PneuExtension extends AbstractExtension
{
    private PneuService $pneuService;

    public function __construct(PneuService $pneuService)
    {
        $this->pneuService = $pneuService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_pneu_hot', [$this, 'isPneuHot']),
            new TwigFunction('is_pneu_new', [$this, 'isPneuNew']),
            new TwigFunction('is_pneu_on_sale', [$this, 'isPneuOnSale']),
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isPneuHot(Pneu $pneu): bool
    {
        return $this->pneuService->isPneuHot($pneu->getId());
    }

    public function isPneuNew(Pneu $pneu): bool
    {
        return $this->pneuService->isPneuNew($pneu);
    }

    public function isPneuOnSale(Pneu $pneu): bool
    {
        return $this->pneuService->isPneuOnSale($pneu);
    }
}
