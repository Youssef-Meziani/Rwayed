<?php

namespace App\DTO;

class AvisDTO
{
    public ?int $id = null;
    public ?int $note = null;
    public ?string $commentaire = null;
    public ?\DateTimeInterface $dateCreation = null;
    public ?string $author = null;
    public ?string $email = null;
    public ?AdherentDTO $adherent = null;
}