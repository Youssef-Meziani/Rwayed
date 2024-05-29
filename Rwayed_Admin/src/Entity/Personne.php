<?php

namespace App\Entity;

use App\Enum\SexeEnum;
use App\Repository\PersonneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "descr",type: "string",length: 10)]
#[ORM\DiscriminatorMap(['Adherent' => Adherent::class,'Technicien' => Technicien::class,'Admin' => Admin::class])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
abstract class Personne implements PasswordAuthenticatedUserInterface,UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    protected ?int $id = null;

    #[ORM\Column(length: 20)]
    protected ?string $nom = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(length: 20)]
    protected ?string $prenom = null;

    #[ORM\Column(length: 15)]
    protected ?string $tele = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    protected ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(length: 50,unique: true)]
    protected ?string $email = null;

    #[ORM\Column(type: Types::JSON)]
    protected array $roles = [];

    #[ORM\Column(length: 120)]
    protected ?string $mot_de_passe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $dernier_connection = null;

    #[ORM\Column(nullable: true)]
    protected ?bool $desactive = false;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

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

    public function setPassword(string $mot_de_passe): static
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?SexeEnum $sexe): self
    {
        $this->sexe = $sexe?->value;
        return $this;
    }

    public function toggleDesactive(): self
    {
        $this->desactive = !$this->desactive;
        return $this;
    }
}
