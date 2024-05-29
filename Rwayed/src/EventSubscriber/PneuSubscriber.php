<?php

namespace App\EventSubscriber;

use App\Entity\Pneu;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Services\PneuService;

class PneuSubscriber implements EventSubscriberInterface
{
    private PneuService $pneuService;

    public function __construct(PneuService $pneuService)
    {
        $this->pneuService = $pneuService;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function onKernelView(ViewEvent $event): void
    {
        $pneu = $event->getControllerResult();
        if ($pneu instanceof Pneu) {
            $isHot = $this->pneuService->isPneuHot($pneu->getId());
            $event->getRequest()->attributes->set('isHot', $isHot);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 0],
        ];
    }
}
