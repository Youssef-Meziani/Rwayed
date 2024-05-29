<?php

namespace App\Events;

use App\Entity\Avis;
use Symfony\Contracts\EventDispatcher\Event;

class AvisDeletedEvent extends Event
{
    public const NAME = 'avis.deleted';

    public function __construct(private Avis $avis)
    {
    }

    public function getAvis(): Avis
    {
        return $this->avis;
    }

    public function getPneuSlug(): string
    {
        return $this->avis->getPneu()->getSlug();
    }
}
