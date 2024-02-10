<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien
{

    #[ORM\OneToOne(inversedBy: 'technicien', targetEntity: Personne::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id_per", nullable: false)]
    #[ORM\Id] // Set as primary key
    private Personne $personne;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $date_recrutement;

    #[ORM\Column(length: 20)]
    private string $statuts;

    public function getDateRecrutement(): ?\DateTimeInterface
    {
        return $this->date_recrutement;
    }

    public function setDateRecrutement(\DateTimeInterface $date_recrutement): self
    {
        $this->date_recrutement = $date_recrutement;

        return $this;
    }

    public function getStatuts(): ?string
    {
        return $this->statuts;
    }

    public function setStatuts(string $statuts): self
    {
        $this->statuts = $statuts;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(Personne $personne): self
    {
        $this->personne = $personne;

        return $this;
    }
}
