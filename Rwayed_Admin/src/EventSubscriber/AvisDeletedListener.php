<?php

namespace App\EventSubscriber;

use App\Events\AvisDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvisDeletedListener implements EventSubscriberInterface
{

    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            AvisDeletedEvent::NAME => 'onAvisDeleted',
        ];
    }

    public function onAvisDeleted(AvisDeletedEvent $event): void
    {
        $avis = $event->getAvis();
        $pneu = $avis->getPneu();

//        $pneu->setScoreTotal($pneu->getScoreTotal() - $avis->getNote());
//        $pneu->setNombreEvaluations($pneu->getNombreEvaluations() - 1);

        $this->entityManager->persist($pneu);
        $this->entityManager->flush();
    }
}
