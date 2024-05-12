<?php

namespace App\EventSubscriber;

use App\Order\Factory\PanierFactoryInterface;
use App\Order\Storage\OrderSessionStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class PanierSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private PanierFactoryInterface $panierFactory,
        private OrderSessionStorage $orderStorage)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $panier = $this->panierFactory->create();
        $subTotal = $this->orderStorage->prixTotalPanier();
        $prixTotal = $this->orderStorage->prixTotalPanier() + $this->orderStorage->totalTaxPanier();
        $totalTax = $this->orderStorage->totalTaxPanier();
        $this->twig->addGlobal('panier', $panier);
        $this->twig->addGlobal('prixTotal', $prixTotal);
        $this->twig->addGlobal('totalTax', $totalTax);
        $this->twig->addGlobal('subTotal', $subTotal);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
