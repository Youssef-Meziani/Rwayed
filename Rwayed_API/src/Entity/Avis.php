<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AvisRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['avis:read']],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 3
)]
#[ApiFilter(SearchFilter::class, properties: ['pneu.slug' => 'exact'])]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Note is required.")]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?int $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['avis:read'])]
    private ?Pneu $pneu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[Groups(['avis:read'])]
    private ?Adherent $adherent = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?string $author = null;

    #[ORM\Column(length: 40, nullable: true)]
    #[Groups(['avis:read', 'pneu:read'])]
    private ?string $email = null;
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
