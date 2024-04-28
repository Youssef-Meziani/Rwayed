<?php

namespace App\Services;

use App\Entity\Photo;
use App\Entity\Pneu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function processPhotoFiles(Pneu $pneu, array $photoFiles): void
    {
        foreach ($photoFiles as $file) {
            if ($file instanceof UploadedFile) {
                $photo = new Photo();
                $photo->setImageFile($file);
                $photo->setPneu($pneu);
                $this->entityManager->persist($photo);
            }
        }
    }
}
