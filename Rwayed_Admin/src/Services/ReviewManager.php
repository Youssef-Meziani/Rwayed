<?php


namespace App\Services;

use App\Entity\Avis;
use App\Events\AvisDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ReviewManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    )
    {}

    public function deleteReview(Avis $review): void
    {
        $pneu = $review->getPneu();
        if ($pneu) {
            $pneu->setNombreEvaluations($pneu->getNombreEvaluations() - 1);
            $pneu->setScoreTotal($pneu->getScoreTotal() - $review->getNote());
            $this->entityManager->persist($pneu);
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();

        $event = new AvisDeletedEvent($review);
        $this->eventDispatcher->dispatch($event, AvisDeletedEvent::NAME);
    }
}
