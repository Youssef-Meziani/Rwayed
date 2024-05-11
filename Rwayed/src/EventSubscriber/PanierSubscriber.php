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
        $prixTotal = $this->orderStorage->prixTotalPanier();
        $totalTax = $this->orderStorage->totalTaxPanier();
        $this->twig->addGlobal('panier', $panier);
        $this->twig->addGlobal('prixTotal', $prixTotal);
        $this->twig->addGlobal('totalTax', $totalTax);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
