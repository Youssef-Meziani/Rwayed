<?php

namespace App\Entity;

use App\Repository\PneuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PneuRepository::class)]
#[Vich\Uploadable]
class Pneu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Assert\NotBlank(message: "Brand is required.")]
    private string $marque;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Vehicle type is required.")]
    private string $typeVehicule;

    #[Vich\UploadableField(mapping: "pneus",fileNameProperty:"image")]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ["marque", "typeVehicule"])]
    private string $slug;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Season is required.")]
    private string $saison;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Unit price is mandatory.")]
    #[Assert\Positive(message: "Unit price must be positive.")]
    private float $prixUnitaire;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Quantity in stock is mandatory.")]
    #[Assert\Positive(message: "Stock quantity must be a positive number.")]
    private int $quantiteStock;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Description is required.")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "The description cannot exceed 1000 characters."
    )]
    private string $description;

    #[ORM\Column(length: 50)]
    private string $taille;

    #[ORM\Column]
    private int $indiceCharge;

    #[ORM\Column(length: 10)]
    private string $indiceVitesse;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    #[ORM\Column]
    private int $scoreTotal = 0;

    #[ORM\Column]
    private int $nombreEvaluations = 0;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Photo::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: LigneCommande::class)]
    private Collection $ligneCommandes;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
        $this->photos = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->ligneCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updatedAt = new \DateTime('now');
        }
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
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

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
            $this->scoreTotal += $avi->getNote();
            $this->nombreEvaluations++;
        }

        return $this;
    }
    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            if ($avi->getPneu() === $this) {
                $this->scoreTotal -= $avi->getNote();
                $this->nombreEvaluations--;
            }
        }

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
            $ligneCommande->setPneu($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getPneu() === $this) {
                $ligneCommande->setPneu(null);
            }
        }

        return $this;
    }
}