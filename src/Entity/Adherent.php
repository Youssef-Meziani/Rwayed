<?php

namespace App\Entity;

use App\Repository\AdherentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent
{
    #[ORM\OneToOne(targetEntity: Personne::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id_per", nullable: false)]
    #[ORM\Id] // Set as primary key
    private Personne $personne;

    #[ORM\Column]
    private int $point_fidelite = 0;


    public function getPointFidelite(): ?int
    {
        return $this->point_fidelite;
    }

    public function setPointFidelite(int $point_fidelite): static
    {
        $this->point_fidelite = $point_fidelite;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(Personne $personne): static
    {
        $this->personne = $personne;

        return $this;
    }
}
