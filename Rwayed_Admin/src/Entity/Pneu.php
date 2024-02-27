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
#[ORM\Entity(repositoryClass: PneuRepository::class)]
#[Vich\Uploadable]
class Pneu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_pneu = null;

    #[ORM\Column(length: 70)]
    private string $marque;

    #[ORM\Column(length: 50)]
    private string $typeVehicule;

    #[Vich\UploadableField(mapping: "pneus",fileNameProperty:"image")]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    private string $image;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ["marque", "typeVehicule"])]
    private string $slug;

    #[ORM\Column(length: 20)]
    private string $saison;

    #[ORM\Column]
    private float $prixUnitaire;

    #[ORM\Column]
    private int $quantiteStock;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    #[ORM\ManyToOne(inversedBy: 'pneus')]
    #[ORM\JoinColumn(name: "id_cara", referencedColumnName: "id_cara", nullable: false)]
    private ?Caracteristique $id_cara = null;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Photo::class, orphanRemoval: true)]
    private Collection $photos;

    public function __construct()
    {
        $this->dateAjout = new \DateTime();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id_pneu;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getIdPneu(): ?int
    {
        return $this->id_pneu;
    }

    public function setIdPneu(?int $id_pneu): void
    {
        $this->id_pneu = $id_pneu;
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
    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
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

    public function getIdCara(): ?Caracteristique
    {
        return $this->id_cara;
    }

    public function setIdCara(?Caracteristique $id_cara): static
    {
        $this->id_cara = $id_cara;

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
