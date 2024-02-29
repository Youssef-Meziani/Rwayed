<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PneuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PneuRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['pneu:read']],
    denormalizationContext: ['groups' => ['pneu:write']]
)]
class Pneu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pneu:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $marque;

    #[ORM\Column(length: 50)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $typeVehicule;

    #[ORM\Column(length: 255)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $image = '';

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ["marque", "typeVehicule"])]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $slug;

    #[ORM\Column(length: 20)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $saison;

    #[ORM\Column]
    #[Groups(['pneu:read', 'pneu:write'])]
    private float $prixUnitaire;

    #[ORM\Column]
    #[Groups(['pneu:read', 'pneu:write'])]
    private int $quantiteStock;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $description;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private \DateTimeInterface $dateAjout;

    #[ORM\ManyToOne(targetEntity: Caracteristique::class, inversedBy: 'pneus')]
    #[ORM\JoinColumn(name: "id_cara", referencedColumnName: "id", nullable: false)]
    #[Groups(['pneu:read'])]
    private ?Caracteristique $caracteristique = null;


    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Photo::class, orphanRemoval: true)]
    #[Groups(['pneu:read'])]
    private Collection $photos;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(string $typeVehicule): static
    {
        $this->typeVehicule = $typeVehicule;

        return $this;
    }
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSaison(): ?string
    {
        return $this->saison;
    }

    public function setSaison(string $saison): static
    {
        $this->saison = $saison;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(float $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCaracteristique(): ?Caracteristique
    {
        return $this->caracteristique;
    }

    public function setCaracteristique(?Caracteristique $caracteristique): void
    {
        $this->caracteristique = $caracteristique;
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

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setPneu($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getPneu() === $this) {
                $photo->setPneu(null);
            }
        }

        return $this;
    }

}
