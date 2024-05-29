<?php

namespace App\Order\Storage;

use App\Entity\Pneu;
use App\OrderManager\OrderManager;

interface OrderStorageInterface
{
    public function ajouterAuPanier(Pneu $pneu, int $qte, bool $isRepair);

    public function supprimerLignePanier(int $id, bool $isRepair);

    public function supprimerPanier(OrderManager $orderManager);

    public function recuprerPanier(): OrderManager;

    public function prixTotalPanier(): float;

    public function modifierLignePanier(string $id, int $qte);
}
