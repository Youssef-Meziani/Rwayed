<?php

namespace App\Order\Storage;

use App\Entity\Pneu;
use App\OrderManager\OrderItemManager;
use App\OrderManager\OrderManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderSessionStorage implements OrderStorageInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private Security $security,
    ) {
    }

    public function ajouterAuPanier(Pneu $pneu, int $qte, bool $isRepair): void
    {
        $panier = $this->recuprerPanier();
        $panier->addItem(new OrderItemManager(
            $pneu->getId(),
            $pneu->getImage(),
            $pneu->getPrixUnitaire(),
            $qte,
            $isRepair,
            $pneu->getMarque(),
            $pneu->getSlug(),
        ));
        $this->enregistrerPanier($panier);
    }

    public function supprimerLignePanier(int $id, bool $isRepair): void
    {
        $key = $id.($isRepair ? '_repaired' : '_not_repaired');
        $panier = $this->recuprerPanier();
        if (array_key_exists($key, $panier->getLines())) {
            $panier->removeItemByKey($key);
            $this->enregistrerPanier($panier);
        }
    }

    public function supprimerPanier(OrderManager $orderManager): void
    {
        $orderManager->vider();
    }

    public function recuprerPanier(): OrderManager
    {
        $session = $this->requestStack->getSession();
        $panier = $session->get('panier');

        if (!$panier) {
            $panier = new OrderManager();
            $panier->setAdherent($this->security->getUser());
            $session->set('panier', $panier);
        }

        return $panier;
    }

    public function prixTotalPanier(): float
    {
        $panier = $this->recuprerPanier();
        $ligneCommandes = $panier->getLines();

        return array_reduce($ligneCommandes, static function ($total, $ligneCommande) {
            return $total + $ligneCommande->getPrix() * $ligneCommande->getQuantity();
        }, 0.0);
    }


    public function totalTaxPanier(): float
    {
        $panier = $this->recuprerPanier();
        $ligneCommandes = $panier->getLines();

        return array_reduce($ligneCommandes, static function ($totalTax, $ligneCommande) {
            return $totalTax + $ligneCommande->getTaxAmount();
        }, 0.0);
    }


    public function modifierLignePanier(int $id, int $qte): void
    {
        $panier = $this->recuprerPanier();
        $ligneCommande = $panier->getLines();
        if (array_key_exists($id, $ligneCommande)) {
            $cartItem = $ligneCommande[$id];
            $cartItem->setQuantity($qte);
            $this->enregistrerPanier($panier);
        }
    }

    private function enregistrerPanier(OrderManager $panier): void
    {
        $session = $this->requestStack->getSession();
        $session->set('panier', $panier);
    }
}
