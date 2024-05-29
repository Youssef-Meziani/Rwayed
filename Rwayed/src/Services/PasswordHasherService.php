<?php

namespace App\Services;

use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHasherService
{
    private UserPasswordHasherInterface $passwordHasher;

    private PasswordHasherService $passwordHasherService;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    public function hashPassword(Personne $personne, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword($personne, $plainPassword);
    }

    public function isPasswordValid(Personne $personne, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($personne, $plainPassword);
    }

    public function setPassword(Personne $personne, string $plainPassword)
    {
        // Hashage du nouveau mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($personne, $plainPassword);

        // Définition du nouveau mot de passe hashé
        $personne->setPassword($hashedPassword);

        // Enregistrement des modifications en base de données
        $this->entityManager->flush();
    }
}
