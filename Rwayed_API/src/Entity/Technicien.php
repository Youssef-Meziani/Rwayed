<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TechnicienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien extends Personne
{
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_recrutement;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    public function __construct()
    {
        $this->date_recrutement = new \DateTime();
    }
    public function getDateRecrutement(): ?\DateTimeInterface
    {
        return $this->date_recrutement;
    }

    public function setDateRecrutement(\DateTimeInterface $date_recrutement): static
    {
        $this->date_recrutement = $date_recrutement;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRoles(): array
    {
        // Vérifie si le tableau $roles est vide avant d'ajouter 'ROLE_USER'
        if (empty($this->roles)) {
            return ['ROLE_USER'];
        }

        return $this->roles;
    }

    public function setRoles(array $roles):self
    {
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
