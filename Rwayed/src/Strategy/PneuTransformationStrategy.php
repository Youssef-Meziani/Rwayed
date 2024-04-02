<?php
namespace App\Strategy;

use App\DTO\PneuDTO;
use App\Entity\Pneu;
use Doctrine\Common\Collections\ArrayCollection;

class PneuTransformationStrategy implements TransformationStrategyInterface
{
    private $photoStrategy;

    public function __construct(TransformationStrategyInterface $photoStrategy)
    {
        $this->photoStrategy = $photoStrategy;
    }

    public function transform($dto)
    {
        if (!$dto instanceof PneuDTO) {
            throw new \InvalidArgumentException('Expected type of PneuDTO');
        }

        $pneu = new Pneu();
        $pneu->setId($dto->id);
        $pneu->setMarque($dto->marque);
        $pneu->setTypeVehicule($dto->typeVehicule);
        $pneu->setImage($dto->image);
        $pneu->setSlug($dto->slug);
        $pneu->setSaison($dto->saison);
        $pneu->setPrixUnitaire($dto->prixUnitaire);
        $pneu->setQuantiteStock($dto->quantiteStock);
        $pneu->setDescription($dto->description);
        $pneu->setDateAjout($dto->dateAjout);
        $pneu->setIndiceCharge($dto->indiceCharge);
        $pneu->setTaille($dto->taille);
        $pneu->setIndiceVitesse($dto->indiceVitesse);
        $pneu->setScoreTotal($dto->scoreTotal);
        $pneu->setNombreEvaluations($dto->nombreEvaluations);

        // Transformation des PhotoDTO en Photo et ajout à la collection de photos de Pneu
        foreach ($dto->photos as $photoDTO) {
            $photo = $this->photoStrategy->transform($photoDTO);
            $pneu->addPhoto($photo);
        }

        return $pneu;
    }
}