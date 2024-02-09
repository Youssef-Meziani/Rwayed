<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: '`admin`')]
class Admin
{

    #[ORM\OneToOne(inversedBy: 'admin', targetEntity: Personne::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id_per", nullable: false)]
    #[ORM\Id] // Set as primary key
    private Personne $personne;

    #[ORM\Column(length: 50)]
    private string $rang;

    #[ORM\Column]
    private bool $is_super;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $date_embauche;

    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): self
    {
        $this->rang = $rang;

        return $this;
    }

    public function isIsSuper(): ?bool
    {
        return $this->is_super;
    }

    public function setIsSuper(bool $is_super): self
    {
        $this->is_super = $is_super;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->date_embauche;
    }

    public function setDateEmbauche(\DateTimeInterface $date_embauche): self
    {
        $this->date_embauche = $date_embauche;

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
