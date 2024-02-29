<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource(normalizationContext: ['groups' => ['photo:read']])]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['photo:read', 'pneu:read'])]
    private int $id;


    #[ORM\Column(length: 255)]
    #[Groups(['photo:read', 'pneu:read'])]
    private string $path;

    #[ORM\ManyToOne(targetEntity: Pneu::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(name: "id_pneu", referencedColumnName: "id", nullable: false)]
    #[Groups(['photo:read'])]
    private ?Pneu $pneu = null;

    public function getId(): ?int
    {
        return $this->id;
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
