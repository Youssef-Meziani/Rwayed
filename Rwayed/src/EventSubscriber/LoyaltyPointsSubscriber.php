<?php

namespace App\EventSubscriber;

use App\Events\CommandeEvent;
use Doctrine\ORM\EntityManagerInterface;

class LoyaltyPointsSubscriber
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function onCommandePlaced(CommandeEvent $event)
    {
        $commande = $event->getCommande();
        $adherent = $commande->getAdherent();
        $adherent->setPointsFidelite($adherent->getPointsFidelite() + 5);

        $this->entityManager->persist($adherent);
        $this->entityManager->flush();
    }
}
