<?php

namespace App\Order\Factory;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Pneu;
use App\Enum\PanierStatus;
use App\Order\Storage\OrderSessionStorage;
use App\OrderManager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PanierFactory
{
    public function __construct(private Security $security, private OrderSessionStorage $storage,private EntityManagerInterface $manager)
    {
    }

    public function create(): Commande
    {
        $commande = new Commande();
        $commande->setAdherent($this->security->getUser());
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setStatutsCommande(PanierStatus::PENDING);
        $commande->setFastLivraison(true);
        $commande->setFraisLivraisons(0.0);
        $commande->setTotal(1000.0);
        $this->load($commande,$this->storage->recuprerPanier());
        return $commande;
    }

    private function load(Commande $commande, OrderManager $orderManager): void
    {
        $lines = $orderManager->getLines();
        $ids = array_map(function($key) {
            return explode('_', $key)[0];
        }, array_keys($lines));
        $pneus = $this->manager->getRepository(Pneu::class)->findBy(['id' => $ids]);
        foreach ($pneus as $item) {
            $id_repaired_status = $item->getId() . (isset($lines[$item->getId() . '_repaired']) ? '_repaired' : '_not_repaired');
            $line = $lines[$id_repaired_status];

            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande)
                ->setPneu($item)
                ->setQuantity($line->getQuantity())
                ->setPrix($line->getPrix())
                ->setWithRepair(false);
            $commande->addLigneCommande($ligneCommande);
        }
    }

}
