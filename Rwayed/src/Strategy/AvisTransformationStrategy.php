<?php

namespace App\Strategy;

use App\DTO\AvisDTO;
use App\Entity\Adherent;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;

class AvisTransformationStrategy implements TransformationStrategyInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($dto)
    {
        if (!$dto instanceof AvisDTO) {
            throw new \InvalidArgumentException('Expected type of AvisDTO');
        }

        $avis = new Avis();
        $avis->setId($dto->id);
        $avis->setNote($dto->note);
        $avis->setCommentaire($dto->commentaire);
        $avis->setDateCreation($dto->dateCreation ?? new \DateTime());
        $avis->setAuthor($dto->author);
        $avis->setEmail($dto->email);

        if (isset($dto->adherent) && $dto->adherent->id !== null) {
            $adherent = $this->entityManager->getRepository(Adherent::class)->find($dto->adherent->id);
            if ($adherent) {
                $avis->setAdherent($adherent);
            }
        }
        return $avis;
    }
}
