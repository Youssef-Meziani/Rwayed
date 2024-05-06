<?php

namespace App\Services;

use App\Entity\Adherent;
use App\Entity\Pneu;
use App\Entity\PneuFavList;
use Doctrine\ORM\EntityManagerInterface;

class WishlistService
{
    private $entityManager;
    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function findPneuInWishlist(Adherent $user, Pneu $pneu): ?PneuFavList
    {
        $pneuFavLists = $user->getPneuFavLists();

        foreach ($pneuFavLists as $pneuFavList) {
            if ($pneuFavList->getPneu() === $pneu) {
                return $pneuFavList;
            }
        }

        return null;
    }
    public function addToWishlist(Adherent $user, Pneu $pneu): bool
    {
        if ($this->findPneuInWishlist($user, $pneu) !== null) {
            return false;
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
        $pneuFavList = $this->findPneuInWishlist($user, $pneu);
        if ($pneuFavList !== null) {
            // Supprimer le pneu de la liste de souhaits de l'utilisateur
            $this->entityManager->remove($pneuFavList);
            $this->entityManager->flush();

            return true;
        }

        // Si le pneu n'est pas trouvé dans la liste de souhaits de l'utilisateur
        return false;
    }
}
