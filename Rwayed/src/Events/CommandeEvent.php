<?php

namespace App\Events;

use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\Commande;

class CommandeEvent extends Event
{
    public const NAME = 'commande.placed';

    private Commande $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}
