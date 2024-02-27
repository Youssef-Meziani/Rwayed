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

    #[ORM\Column(length: 255)]
    private string $path;

    #[ORM\ManyToOne(targetEntity: Pneu::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(name: "id_pneu", referencedColumnName: "id_pneu", nullable: false)]
    private ?Pneu $pneu = null;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
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
