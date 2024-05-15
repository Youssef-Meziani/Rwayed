<?php


namespace App\Services;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;

class ReviewManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
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
    }
}
