<?php

namespace App\EventSubscriber;

use App\Events\PneuStockEvent;

use Doctrine\ORM\EntityManagerInterface;

class PneuStockListener
{
    const NAME = "pneu.stock";

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function onOrderPlaced(PneuStockEvent $event)
    {
        foreach ($event->getPneus() as $item) {
            $item->getPneu()->decreaseStock($item->getQuantity());
            $this->entityManager->persist($item->getPneu());
        }
        $this->entityManager->flush();
    }
}
