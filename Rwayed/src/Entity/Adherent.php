<?php

namespace App\Entity;

use App\Repository\AdherentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent extends Personne
{
    #[ORM\Column]
    private ?int $points_fidelite;
    public function __construct()
    {
        $this->points_fidelite = 0;
        $this->roles = ['ROLE_ADHERENT'];
    }

    public function getPointsFidelite(): ?int
    {
        return $this->points_fidelite;
    }

    public function setPointsFidelite(int $points_fidelite): static
    {
        $this->points_fidelite = $points_fidelite;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_ADHERENT', $roles, true)) {
            $roles[] = 'ROLE_ADHERENT';
        }
        return $roles;
    }

    public function setRoles(array $roles): self
    {
        if (!in_array('ROLE_ADHERENT', $roles, true)) {
            $roles[] = 'ROLE_ADHERENT';
        }
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}