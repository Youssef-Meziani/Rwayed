<?php

namespace App\EventSubscriber;

use App\Events\AvisEvent;
use App\Services\ApiPlatformConsumerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/*
 * Ce listener écoute l'événement avis.submitted et met à jour les propriétés du pneu.
 */
class AvisListener implements EventSubscriberInterface
{
    private $cache;
    public function __construct(private EntityManagerInterface $entityManager,private ApiPlatformConsumerService $apiPlatformConsumerService,)
    {
        $this->cache = new FilesystemAdapter();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AvisEvent::NAME => 'onAvisSubmitted',
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function onAvisSubmitted(AvisEvent $event): void
    {
        $avis = $event->getAvis();
        $pneu = $avis->getPneu();

        $pneu->setScoreTotal($pneu->getScoreTotal() + $avis->getNote());
        $pneu->setNombreEvaluations($pneu->getNombreEvaluations() + 1);

        $this->entityManager->persist($pneu);
        $this->entityManager->flush();

        // Invalidate the cache for the updated pneu
        $this->cache->delete('fetch_pneu_by_slug_' . $event->getPneuSlug());
        $this->cache->delete('fetch_pneu_by_id_' . $pneu->getId());

        // Invalidate the cache for the list of pneus
        $this->apiPlatformConsumerService->invalidateFetchPneusCache();
    }
}
