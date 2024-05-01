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
        // Vérifier si le pneu est déjà dans la liste de souhaits de l'utilisateur
        $wishlistEntry = $this->entityManager->getRepository(PneuFavList::class)->findOneBy([
            'adherent' => $user,
            'pneu' => $pneu,
        ]);
        if ($wishlistEntry) {
            return false; // Le pneu est déjà dans la liste de souhaits
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
        // Rechercher pneu correspondante dans la liste de souhaits de l'utilisateur
        $wishlistEntry = $this->entityManager->getRepository(PneuFavList::class)->findOneBy([
            'adherent' => $user,
            'pneu' => $pneu,
        ]);

        // Si pneu existe, le supprimer
        if ($wishlistEntry) {
            $this->entityManager->remove($wishlistEntry);
            $this->entityManager->flush();

            return true;
        } else {
            return false;
        }
    }
}
