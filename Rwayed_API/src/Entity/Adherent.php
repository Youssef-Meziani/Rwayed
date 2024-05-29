<?php

namespace App\Entity;

use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent extends Personne
{
    #[ORM\Column]
    private ?int $points_fidelite;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Avis::class)]
    private Collection $avis;
    public function __construct()
    {
        $this->points_fidelite = 0;
        $this->roles = ['ROLE_ADHERENT'];
        $this->avis = new ArrayCollection();
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

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setAdherent($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getAdherent() === $this) {
                $avi->setAdherent(null);
            }
        }

        return $this;
    }
}