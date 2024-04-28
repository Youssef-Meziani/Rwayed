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
    public ?float $prixUnitaire = null;
    public int $quantiteStock;
    public string $description;
    public \DateTimeInterface $dateAjout; // Ajoutée
    public string $taille;
    public int $indiceCharge;
    public string $indiceVitesse;
    public int $scoreTotal;
    public int $nombreEvaluations;

    /**
     * @var PhotoDTO[] 
     */
    public array $photos = [];

    /**
     * @var AvisDTO[]
     */
    public array $avis = [];

    public function addPhoto(PhotoDTO $photo): void {
        $this->photos[] = $photo;
    }

    /**
     * @return PhotoDTO[]
     */
    public function getPhotos(): array {
        return $this->photos;
    }

    public function setPhotos(array $photos): void 
    {
        foreach ($photos as $photo) {
            if (!$photo instanceof PhotoDTO) {
                throw new \InvalidArgumentException('All photos must be instances of PhotoDTO');
            }
        }
        $this->photos = $photos;
    }
}