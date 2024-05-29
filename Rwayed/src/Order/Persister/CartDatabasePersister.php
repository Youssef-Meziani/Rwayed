<?php

namespace App\Order\Persister;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;

class CartDatabasePersister implements CartPersisterInterface
{
    public function __construct(private EntityManagerInterface $manager,)
    {}

    public function persist(Commande $commande): void
    {
        foreach ($commande->getLigneCommandes() as $cartItem) {
            $this->manager->persist($cartItem);
        }
        $this->manager->persist($commande);
        $this->manager->flush();
    }
}
