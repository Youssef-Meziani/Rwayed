<?php

namespace App\Entity;

use App\Enum\PanierStatus;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column]
    private ?float $fraisLivraisons = null;

    #[ORM\Column]
    private ?bool $FastLivraison = null;

    #[ORM\Column(length: 20)]
    private ?PanierStatus $statutsCommande = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Adherent $adherent = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    #[ORM\ManyToOne(inversedBy: 'commande')]
    private ?CodePromo $codePromo = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $codeUnique = null;

    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getFraisLivraisons(): ?float
    {
        return $this->fraisLivraisons;
    }

    public function setFraisLivraisons(float $fraisLivraisons): static
    {
        $this->fraisLivraisons = $fraisLivraisons;

        return $this;
    }

    public function isFastLivraison(): ?bool
    {
        return $this->FastLivraison;
    }

    public function setFastLivraison(bool $FastLivraison): static
    {
        $this->FastLivraison = $FastLivraison;

        return $this;
    }

    public function getStatutsCommande(): ?\App\Enum\PanierStatus
    {
        return $this->statutsCommande;
    }

    public function setStatutsCommande($statutsCommande): self
    {
        if (is_string($statutsCommande)) {
            $statutsCommande = PanierStatus::from($statutsCommande);
        }
        $this->statutsCommande = $statutsCommande;
        return $this;
    }


    public function getStatutsCommandeLabel(): string
    {
        return $this->statutsCommande->label();
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
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

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getCodePromo(): ?CodePromo
    {
        return $this->codePromo;
    }

    public function setCodePromo(?CodePromo $codePromo): static
    {
        $this->codePromo = $codePromo;

        return $this;
    }

    public function computeTotal(): float
    {
        return \array_reduce(
            $this->getLigneCommandes()->toArray(),
            static fn (float $total, LigneCommande $item) => $total += $item->getItemTotal() * 1.2,
            0
        );
    }

    public function getCodeUnique(): ?string
    {
        return $this->codeUnique;
    }

    public function setCodeUnique(string $codeUnique): self
    {
        $this->codeUnique = $codeUnique;

        return $this;
    }
}
