<?php

namespace App\Services;

use App\Entity\Adherent;
use App\Entity\Pneu;
use App\Entity\PneuFavList;
use Doctrine\ORM\EntityManagerInterface;

class WishlistService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToWishlist(Adherent $user, Pneu $pneu): bool
    {
        $pneuFavLists = $user->getPneuFavLists();

        foreach ($pneuFavLists as $pneuFavList) {
            if ($pneuFavList->getPneu() === $pneu) {
                return false; // Le pneu est déjà dans la liste des favoris de l'utilisateur
            }
        }
        // Créer une nouvelle entité PneuFavList
        $pneuFavList = new PneuFavList();
        $pneuFavList->setAdherent($user);
        $pneuFavList->setPneu($pneu);
        $pneuFavList->setDateAjout(new \DateTime());

        // Enregistrer la nouvelle entité PneuFavList
        $this->entityManager->persist($pneuFavList);
        $this->entityManager->flush();

        return true;
    }

    public function removeFromWishlist(Adherent $user, Pneu $pneu): bool
    {
        $pneuFavLists = $user->getPneuFavLists();

        foreach ($pneuFavLists as $pneuFavList) {
            if ($pneuFavList->getPneu() === $pneu) {
                // Supprimer le pneu de la liste de souhaits de l'utilisateur
                $this->entityManager->remove($pneuFavList);
                $this->entityManager->flush();
                
                return true;
            }
        }

        // Si le pneu n'est pas trouvé dans la liste de souhaits de l'utilisateur
        return false;
    }
}
