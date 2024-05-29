<?php

namespace App\Entity;

use App\Enum\CouponStatus;
use App\Repository\CodePromoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodePromoRepository::class)]
#[ORM\UniqueConstraint(columns: ['code'])]
class CodePromo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $pourcentage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateExpiration = null;

    #[ORM\Column(type: 'string', enumType: CouponStatus::class)]
    private ?CouponStatus $status = null;

    #[ORM\OneToMany(mappedBy: 'codePromo', targetEntity: Commande::class)]
    private Collection $commande;

    public function __construct()
    {
        $this->commande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?CouponStatus
    {
        return $this->status;
    }

    public function setStatus(CouponStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function getPourcentage(): ?int
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?int $pourcentage): void
    {
        $this->pourcentage = $pourcentage;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommande(): Collection
    {
        return $this->commande;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commande->contains($commande)) {
            $this->commande->add($commande);
            $commande->setCodePromo($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getCodePromo() === $this) {
                $commande->setCodePromo(null);
            }
        }

        return $this;
    }
}
