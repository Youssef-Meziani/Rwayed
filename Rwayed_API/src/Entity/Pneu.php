<?php

namespace App\Entity;


use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Doctrine\Filter\NoteMoyenneFilter;
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
    denormalizationContext: ['groups' => ['pneu:write']],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 16
)]
#[ApiFilter(SearchFilter::class, properties: ['saison' => 'exact','slug' => 'exact'])]
#[ApiFilter(RangeFilter::class, properties: ['prixUnitaire', 'scoreTotal'])]
#[ApiFilter(NoteMoyenneFilter::class)]
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

    #[ORM\Column(length: 50)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $taille;

    #[ORM\Column]
    #[Groups(['pneu:read', 'pneu:write'])]
    private int $indiceCharge;

    #[ORM\Column(length: 10)]
    #[Groups(['pneu:read', 'pneu:write'])]
    private string $indiceVitesse;

    #[ORM\Column]
    #[Groups(['pneu:read', 'pneu:write'])]
    private int $scoreTotal = 0;

    #[ORM\Column]
    #[Groups(['pneu:read', 'pneu:write'])]
    private int $nombreEvaluations = 0;


    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Photo::class, orphanRemoval: true)]
    #[Groups(['pneu:read'])]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Avis::class)]
    #[Groups(['pneu:read'])]
    private Collection $avis;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
        $this->photos = new ArrayCollection();
        $this->avis = new ArrayCollection();
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

  
    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

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

    public function getScoreTotal(): ?int
    {
        return $this->scoreTotal;
    }

    public function setScoreTotal(int $scoreTotal): self
    {
        $this->scoreTotal = $scoreTotal;
        return $this;
    }

    public function getNombreEvaluations(): ?int
    {
        return $this->nombreEvaluations;
    }

    public function setNombreEvaluations(int $nombreEvaluations): self
    {
        $this->nombreEvaluations = $nombreEvaluations;
        return $this;
    }
    #[Groups(['pneu:read'])]
    public function getNoteMoyenne(): ?float
    {
        if ($this->nombreEvaluations === 0) {
            return 0.0;
        }
        return $this->scoreTotal / $this->nombreEvaluations;
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

    public function getAvis(): Collection
    {
        return $this->avis;
    }

}
