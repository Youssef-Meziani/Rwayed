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
    #[Assert\NotBlank(message: "La marque est obligatoire.")]
    private string $marque;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le type de véhicule est obligatoire.")]
    private string $typeVehicule;

    #[Vich\UploadableField(mapping: "pneus",fileNameProperty:"image")]
//    #[Assert\Image(maxSize: "3M", mimeTypes: ["image/jpeg", "image/png"], mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG).")]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Gedmo\Slug(fields: ["marque", "typeVehicule"])]
    private string $slug;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "La saison est obligatoire.")]
    private string $saison;

    #[ORM\Column]
    #[Assert\Type(type: "float", message: "Le prix unitaire doit être un nombre.")]
    private float $prixUnitaire;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La quantité en stock est obligatoire.")]
    #[Assert\Type(type: "integer", message: "La quantité en stock doit être un nombre entier.")]
    private int $quantiteStock;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    private string $description;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $dateAjout;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'pneus')]
    #[ORM\JoinColumn(name: "id_cara", referencedColumnName: "id", nullable: false)]
    private ?Caracteristique $caracteristique = null;

    #[ORM\OneToMany(mappedBy: 'pneu', targetEntity: Photo::class, cascade: ['persist', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
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




    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
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

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): static
    {
        $this->dateAjout = $dateAjout;

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