<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[Vich\Uploadable]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[Vich\UploadableField(mapping: "pneus",fileNameProperty:"path")]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $path = null;

    #[ORM\ManyToOne(targetEntity: Pneu::class, fetch: 'EAGER', inversedBy: 'photos')]
    #[ORM\JoinColumn(name: "id_pneu", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Pneu $pneu = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPneu(): ?Pneu
    {
        return $this->pneu;
    }

    public function setPneu(?Pneu $pneu): static
    {
        $this->pneu = $pneu;

        return $this;
    }
}
