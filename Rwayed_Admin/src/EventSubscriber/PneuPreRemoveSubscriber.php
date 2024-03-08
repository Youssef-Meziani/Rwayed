<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use App\Entity\Pneu;
use Doctrine\ORM\EntityManagerInterface;

class PneuPreRemoveSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Pneu) {
            return;
        }

        foreach ($entity->getPhotos() as $photo) {
            $this->entityManager->remove($photo);
        }
    }
}
