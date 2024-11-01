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


    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Adresse::class, orphanRemoval: true)]
    private Collection $adresses;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: PneuFavList::class, orphanRemoval: true)]
    private Collection $pneuFavLists;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Commande::class)]
    private Collection $commandes;


    public function __construct()
    {
        $this->points_fidelite = 0;
        $this->roles = ['ROLE_ADHERENT'];

        $this->adresses = new ArrayCollection();
        $this->pneuFavLists = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->commandes = new ArrayCollection();

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

     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

   public function addAdress(Adresse $adress): static
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
            $adress->setAdherent($this);
        }

        return $this;
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


   public function removeAdress(Adresse $adress): static
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getAdherent() === $this) {
                $adress->setAdherent(null);
            }
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

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setAdherent($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getAdherent() === $this) {
                $commande->setAdherent(null);
            }
        }

        return $this;
    }
}