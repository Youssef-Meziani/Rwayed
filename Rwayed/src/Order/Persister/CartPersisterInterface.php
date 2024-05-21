<?php


namespace App\Order\Persister;

use App\Entity\Commande;


interface CartPersisterInterface 
{
    public function persist(Commande $commande): void;
}
