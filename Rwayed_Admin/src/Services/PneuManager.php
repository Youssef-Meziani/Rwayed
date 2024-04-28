<?php

namespace App\Services;

use App\Entity\Pneu;
use App\Entity\Photo;
use App\Interfaces\PneuManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class PneuManager implements PneuManagerInterface
{
    private $entityManager;
    private $photoService;

    public function __construct(EntityManagerInterface $entityManager, PhotoService $photoService)
    {
        $this->entityManager = $entityManager;
        $this->photoService = $photoService;
    }

    public function createPneu(Pneu $pneu, array $photoFiles): void
    {
        $this->photoService->processPhotoFiles($pneu, $photoFiles);
        $this->entityManager->persist($pneu);
        $this->entityManager->flush();
    }


    public function editPneu(Pneu $pneu): void
    {
        $this->entityManager->flush();
    }

    public function deletePneu(string $identifier): JsonResponse
    {
        $pneu = $this->entityManager->getRepository(Pneu::class)->findOneBy(['slug' => $identifier]);

        if (!$pneu) {
            return new JsonResponse(['success' => false, 'message' => 'Tire not found'], 404);
        }

        try {
            $this->entityManager->remove($pneu);
            $this->entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => 'Tire successfully removed']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Error removing tire: ' . $e->getMessage()], 500);
        }
    }
}
