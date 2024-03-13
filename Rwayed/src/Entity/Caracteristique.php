<?php

namespace App\Entity;

use App\Repository\CaracteristiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaracteristiqueRepository::class)]
class Caracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 50)]
    private string $taille;

    #[ORM\Column]
    private int $indiceCharge;

    #[ORM\Column(length: 10)]
    private string $indiceVitesse;

    #[ORM\OneToMany(mappedBy: 'caracteristique', targetEntity: Pneu::class)]
    private Collection $pneus;

    public function __construct()
    {
        $this->pneus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getIndiceCharge(): ?int
    {
        return $this->indiceCharge;
    }

    public function setIndiceCharge(int $indiceCharge): static
    {
        $this->indiceCharge = $indiceCharge;

        return $this;
    }

    public function getIndiceVitesse(): ?string
    {
        return $this->indiceVitesse;
    }

    public function setIndiceVitesse(string $indiceVitesse): static
    {
        $this->indiceVitesse = $indiceVitesse;

        return $this;
    }

    /**
     * @return Collection<int, Pneu>
     */
    public function getPneus(): Collection
    {
        return $this->pneus;
    }

    public function addPneu(Pneu $pneu): static
    {
        if (!$this->pneus->contains($pneu)) {
            $this->pneus->add($pneu);
            $pneu->setCaracteristique($this);
        }

        return $this;
    }

    public function removePneu(Pneu $pneu): static
    {
        if ($this->pneus->removeElement($pneu)) {
            // set the owning side to null (unless already changed)
            if ($pneu->getCaracteristique() === $this) {
                $pneu->setCaracteristique(null);
            }
        }

        return $this;
    }
}
