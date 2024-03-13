<?php
namespace App\Strategy;

use App\DTO\PneuDTO;
use App\Entity\Pneu;
use Doctrine\Common\Collections\ArrayCollection;

class PneuTransformationStrategy implements TransformationStrategyInterface
{
    private $caracteristiqueStrategy;
    private $photoStrategy;

    public function __construct(TransformationStrategyInterface $caracteristiqueStrategy, TransformationStrategyInterface $photoStrategy)
    {
        $this->caracteristiqueStrategy = $caracteristiqueStrategy;
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
        // Transformation de CaracteristiqueDTO en Caracteristique
        if ($dto->caracteristique !== null) {
            $caracteristique = $this->caracteristiqueStrategy->transform($dto->caracteristique);
            $pneu->setCaracteristique($caracteristique);
        }

        // Transformation des PhotoDTO en Photo et ajout à la collection de photos de Pneu
        foreach ($dto->photos as $photoDTO) {
            $photo = $this->photoStrategy->transform($photoDTO);
            $pneu->addPhoto($photo);
        }

        return $pneu;
    }
}