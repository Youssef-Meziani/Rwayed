<?php

namespace App\Events;

use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\EventDispatcher\Event;

class PneuStockEvent extends Event
{
    public const NAME = 'pneu.stock';

    public function __construct(private Collection $pneus)
    {
    }

    public function getPneus(): Collection
    {
        return $this->pneus;
    }

}
