<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CaracteristiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CaracteristiqueRepository::class)]
#[ApiResource(normalizationContext: ['groups' => ['caracteristique:read']])]
class Caracteristique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['caracteristique:read', 'pneu:read'])]
    private int $id;

    #[ORM\Column(length: 50)]
    #[Groups(['caracteristique:read', 'pneu:read'])]
    private string $taille;

    #[ORM\Column]
    #[Groups(['caracteristique:read', 'pneu:read'])]
    private int $indiceCharge;

    #[ORM\Column(length: 10)]
    #[Groups(['caracteristique:read', 'pneu:read'])]
    private string $indiceVitesse;

    #[ORM\OneToMany(mappedBy: 'caracteristique', targetEntity: Pneu::class)]
    #[Groups(['caracteristique:read'])]
    private Collection $pneus;

    public function __construct()
    {
        $this->pneus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $pneu->setIdCara($this);
        }

        return $this;
    }

    public function removePneu(Pneu $pneu): static
    {
        // set the owning side to null (unless already changed)
        if ($this->pneus->removeElement($pneu) && $pneu->getIdCara() === $this) {
            $pneu->setIdCara(null);
        }

        return $this;
    }
}
