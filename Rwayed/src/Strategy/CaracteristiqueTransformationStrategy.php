<?php
namespace App\Strategy;

use App\DTO\CaracteristiqueDTO;
use App\Entity\Caracteristique;

class CaracteristiqueTransformationStrategy implements TransformationStrategyInterface
{
    public function transform($dto)
    {
        if (!$dto instanceof CaracteristiqueDTO) {
            throw new \InvalidArgumentException('Expected type of CaracteristiqueDTO');
        }

        $caracteristique = new Caracteristique();
        $caracteristique->setId($dto->id);
        $caracteristique->setTaille($dto->taille);
        $caracteristique->setIndiceCharge($dto->indiceCharge);
        $caracteristique->setIndiceVitesse($dto->indiceVitesse);

        return $caracteristique;
    }
}