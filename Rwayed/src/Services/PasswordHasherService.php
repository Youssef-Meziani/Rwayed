<?php

namespace App\Services;

use App\Entity\Personne;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordHasherService
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hashPassword(Personne $personne, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword($personne, $plainPassword);
    }

    public function isPasswordValid(Personne $personne, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($personne, $plainPassword);
    }
}


