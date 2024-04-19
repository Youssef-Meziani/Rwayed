<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent extends Personne
{
    #[ORM\Column]
    private ?int $points_fidelite;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: PneuFavList::class, orphanRemoval: true)]
    private Collection $pneuFavLists;
    public function __construct()
    {
        $this->points_fidelite = 0;
        $this->roles = ['ROLE_ADHERENT'];
        $this->pneuFavLists = new ArrayCollection();
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
     * @return Collection<int, PneuFavList>
     */
    public function getPneuFavLists(): Collection
    {
        return $this->pneuFavLists;
    }

    public function addPneuFavList(PneuFavList $pneuFavList): static
    {
        if (!$this->pneuFavLists->contains($pneuFavList)) {
            $this->pneuFavLists->add($pneuFavList);
            $pneuFavList->setAdherent($this);
        }

        return $this;
    }

    public function removePneuFavList(PneuFavList $pneuFavList): static
    {
        if ($this->pneuFavLists->removeElement($pneuFavList)) {
            // set the owning side to null (unless already changed)
            if ($pneuFavList->getAdherent() === $this) {
                $pneuFavList->setAdherent(null);
            }
        }

        return $this;
    }
}