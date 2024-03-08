<?php

namespace App\Entity;

use App\Repository\EmailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Unique;

#[ORM\Entity(repositoryClass: EmailsRepository::class)]
class Emails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $Email_address = null;

    #[ORM\ManyToMany(targetEntity: Informations::class, mappedBy: 'emails')]
    private Collection $informations;

    public function __construct()
    {
        $this->informations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailAddress(): ?string
    {
        return $this->Email_address;
    }

    public function setEmailAddress(string $Email_address): static
    {
        $this->Email_address = $Email_address;

        return $this;
    }

    /**
     * @return Collection<int, Informations>
     */
    public function getInformations(): Collection
    {
        return $this->informations;
    }

    public function addInformation(Informations $information): static
    {
        if (!$this->informations->contains($information)) {
            $this->informations->add($information);
            $information->addEmail($this);
        }

        return $this;
    }

    public function removeInformation(Informations $information): static
    {
        if ($this->informations->removeElement($information)) {
            $information->removeEmail($this);
        }

        return $this;
    }
}
