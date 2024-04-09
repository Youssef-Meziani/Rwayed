<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: '`admin`')]
class Admin extends Personne
{

    #[ORM\Column(length: 50)]
    private ?string $rang = null;

    #[ORM\Column]
    private ?bool $is_super = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_embauche;

    #[ORM\OneToMany(mappedBy: 'id_adm', targetEntity: Informations::class)]
    private Collection $informations;

    public function __construct()
    {
        $this->date_embauche = new \DateTime();
        $this->informations = new ArrayCollection();
    }
    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function isIsSuper(): ?bool
    {
        return $this->is_super;
    }

    public function setIsSuper(bool $is_super): static
    {
        $this->is_super = $is_super;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->date_embauche;
    }

    public function setDateEmbauche(\DateTimeInterface $date_embauche): static
    {
        $this->date_embauche = $date_embauche;

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

    /**
     * @return Collection<int, Informations>
     */
    public function getInformations(): Collection
    {
        return $this->informations;
    }

    public function addInformation(Informations $information): static
    {
        if (!$this->informations->contains($information)) {
            $this->informations->add($information);
            $information->setIdAdm($this);
        }

        return $this;
    }

    public function removeInformation(Informations $information): static
    {
        if ($this->informations->removeElement($information)) {
            // set the owning side to null (unless already changed)
            if ($information->getIdAdm() === $this) {
                $information->setIdAdm(null);
            }
        }

        return $this;
    }
}
