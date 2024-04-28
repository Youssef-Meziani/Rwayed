<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PneuFavListRepository;

#[ORM\Entity(repositoryClass: PneuFavListRepository::class)]
class PneuFavList
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Adherent::class, inversedBy: "pneuFavLists")]
    #[ORM\JoinColumn(nullable: false, name: "adherent_id", referencedColumnName: "id")]
    private ?Adherent $adherent = null;


    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Pneu::class, inversedBy: "pneuFavLists")]
    #[ORM\JoinColumn(nullable: false, name: "pneu_id", referencedColumnName: "id")]
    private ?Pneu $pneu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): static
    {
        $this->adherent = $adherent;

        return $this;
    }

    public function getPneu(): ?Pneu
    {
        return $this->pneu;
    }

    public function setPneu(?Pneu $pneu): static
    {
        $this->pneu = $pneu;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }
}
