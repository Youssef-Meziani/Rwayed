<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne implements PasswordAuthenticatedUserInterface,UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id_per;

    #[ORM\Column(length: 20)]
    private string $nom;

    #[ORM\Column(length: 20)]
    private string $prenom;

    #[ORM\Column(length: 13)]
    private string $tele;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $date_naissance;

    #[ORM\Column(length: 10)]
    private string $role;

    #[ORM\Column(length: 60)]
    private string $email;

    #[ORM\Column(length: 90)]
    private string $mot_de_passe;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $derniere_connection;
    #[ORM\Column]
    private bool $locked = false;

    #[ORM\OneToOne(mappedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Technicien $technicien = null;

    #[ORM\OneToOne(mappedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Admin $admine = null;

    #[ORM\OneToOne(mappedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Adherent $adherent = null;
    public function __construct()
    {
        $this->derniere_connection = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id_per;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTele(): ?string
    {
        return $this->tele;
    }

    public function setTele(string $tele): self
    {
        $this->tele = $tele;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getDerniereConnection(): ?\DateTimeInterface
    {
        return $this->derniere_connection;
    }

    public function setDerniereConnection(?\DateTimeInterface $derniere_connection): self
    {
        $this->derniere_connection = $derniere_connection;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(Technicien $technicien): self
    {
        // set the owning side of the relation if necessary
        if ($technicien->getPersonne() !== $this) {
            $technicien->setPersonne($this);
        }

        $this->technicien = $technicien;

        return $this;
    }

    public function getAdmine(): ?Admin
    {
        return $this->admine;
    }

    public function setAdmine(Admin $admine): self
    {
        // set the owning side of the relation if necessary
        if ($admine->getPersonne() !== $this) {
            $admine->setPersonne($this);
        }

        $this->admine = $admine;

        return $this;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(Adherent $adherent): self
    {
        // set the owning side of the relation if necessary
        if ($adherent->getPersonne() !== $this) {
            $adherent->setPersonne($this);
        }

        $this->adherent = $adherent;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function setPassword(string $password): self
    {
        $this->mot_de_passe = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role)];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
