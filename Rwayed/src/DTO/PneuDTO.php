<?php

namespace App\DTO;

class PneuDTO
{
    public int $id;
    public string $marque;
    public string $typeVehicule;
    public string $image;
    public string $slug;
    public string $saison;
    public float $prixUnitaire;
    public int $quantiteStock;
    public string $description;
    public \DateTimeInterface $dateAjout; // Ajoutée
    public ?CaracteristiqueDTO $caracteristique = null;
    public array $photos = [];
}
