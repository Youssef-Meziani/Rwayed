<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Services\PasswordHasherService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "role",type: "string")]
#[ORM\DiscriminatorMap(['Adherent' => Adherent::class,'Technicien' => Technicien::class,'Admin' => Admin::class])]
abstract class Personne implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    protected ?int $id = null;

    #[ORM\Column(length: 20)]
    protected ?string $nom = null;

    #[ORM\Column(length: 20)]
    protected ?string $prenom = null;

    #[ORM\Column(length: 15)]
    protected ?string $tele = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected ?\DateTimeInterface $date_naissance = null;

//    #[ORM\Column(length: 10)]
//    protected ?string $role = null;

    #[ORM\Column(length: 50,unique: true)]
    protected ?string $email = null;

    #[ORM\Column(length: 120)]
    protected ?string $mot_de_passe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $dernier_connection = null;

    #[ORM\Column(nullable: true)]
    protected ?bool $desactive = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTele(): ?string
    {
        return $this->tele;
    }

    public function setTele(string $tele): static
    {
        $this->tele = $tele;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getDernierConnection(): ?\DateTimeInterface
    {
        return $this->dernier_connection;
    }

    public function setDernierConnection(?\DateTimeInterface $dernier_connection): static
    {
        $this->dernier_connection = $dernier_connection;

        return $this;
    }

    public function isDesactive(): ?bool
    {
        return $this->desactive;
    }

    public function setDesactive(?bool $desactive): static
    {
        $this->desactive = $desactive;

        return $this;
    }
}
