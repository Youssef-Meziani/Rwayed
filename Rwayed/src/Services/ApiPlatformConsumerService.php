<?php

namespace App\Services;

use App\DTO\AvisDTO;
use App\DTO\PneuDTO;
use App\Entity\Adherent;
use App\Services\Query\QueryStringBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPlatformConsumerService
{
    public const DEFAULT_COUNT_ITEMS_PER_PAGE = 16;
    public const DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS = 3;

    private HttpClientInterface $client;
    private SerializerInterface $serializer;
    private EntityManagerInterface $entityManager;

    private QueryStringBuilder $queryStringBuilder;
    private $cache;

    public function __construct(HttpClientInterface $apiPlatformClient, SerializerInterface $serializer, EntityManagerInterface $entityManager,QueryStringBuilder $queryStringBuilder,)
    {
        $this->client = $apiPlatformClient;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->queryStringBuilder = $queryStringBuilder;
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fetchPneus(int $page = 1, int $itemsPerPage = self::DEFAULT_COUNT_ITEMS_PER_PAGE): array
    {
        $cacheKey = sprintf('fetch_pneus_%d_%d', $page, $itemsPerPage);
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page, $itemsPerPage) {
            $item->expiresAfter(300); // Cache for 5 minutes

            return $this->fetchResources('pneus', [
                'page' => $page,
                'itemsPerPage' => $itemsPerPage,
            ], PneuDTO::class);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function invalidateFetchPneusCache(): void
    {
        $this->cache->deleteItems(array_map(static function($page) {
            return sprintf('fetch_pneus_%d_%d', $page, self::DEFAULT_COUNT_ITEMS_PER_PAGE);
        }, range(1, 100)));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fetchPneuById(int $id): ?PneuDTO
    {
        $cacheKey = 'fetch_pneu_by_id_' . $id;
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(300); // Cache for 5 minutes

            return $this->fetchResource('pneus/'.$id, PneuDTO::class);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fetchPneuBySlug(string $slug): ?PneuDTO
    {
        $cacheKey = 'fetch_pneu_by_slug_' . $slug;
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($slug) {
            $item->expiresAfter(300); // Cache for 5 minutes

            $response = $this->fetchResources('pneus', ['slug' => $slug], PneuDTO::class);
            return $response ? $response[0] : null;
        });
    }

    /**
     * @throws \JsonException
     */
    public function searchBy(array $searchCriteria, int $page = 1, int $itemsPerPage = self::DEFAULT_COUNT_ITEMS_PER_PAGE): array
    {
        $queryString = $this->queryStringBuilder->addPriceRangeParameter($searchCriteria['prix'] ?? null)
        -> addSaison($searchCriteria['saison'] ?? null)
        -> addNoteMoyenne($searchCriteria['noteMoyenne'] ?? null);
//        dd($queryString->getQueryString());
        return $this->fetchResources('pneus?' . $queryString->getQueryString(),[
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ],PneuDTO::class);

    }


    public function getTotalItems(): int
    {
        try {
            $response = $this->client->request('GET', 'pneus');
            $decodedData = $response->toArray();
            return $decodedData['hydra:totalItems'] ?? 0;
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return 0;
        }
    }

    public function getTotalItemsInSearch(array $searchCriteria = []): int
    {
        try {
            $queryString = $this->queryStringBuilder
                ->addPriceRangeParameter($searchCriteria['prix'] ?? null)
                ->addSaison($searchCriteria['saison'] ?? null)
                ->addNoteMoyenne($searchCriteria['noteMoyenne'] ?? null)
                ->getQueryString();

            $response = $this->client->request('GET', 'pneus?' . $queryString);
            $decodedData = $response->toArray();
            return $decodedData['hydra:totalItems'] ?? 0;
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return 0;
        }
    }

    public function getTotalMembers(): int
    {
        try {
            return $this->entityManager->getRepository(Adherent::class)->count([]);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @throws \JsonException
     */
    public function fetchAvisByPneuSlug(string $slug, int $page = 1, int $itemsPerPage = self::DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS): array
    {
        $result = $this->fetchResourcesWithTotal('aviss', [
            'pneu.slug' => $slug,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ], AvisDTO::class);

        return [
            'avis' => $result['items'],
            'totalAvis' => $result['totalItems'],
        ];
    }

    private function fetchResourcesWithTotal(string $endpoint, array $query, string $type): array
    {
        try {
            $response = $this->client->request('GET', $endpoint, ['query' => $query]);
            $decodedData = $response->toArray();

            $items = isset($decodedData['hydra:member'])
                ? $this->serializer->deserialize(json_encode($decodedData['hydra:member'], JSON_THROW_ON_ERROR), $type.'[]', 'json')
                : [];
            $totalItems = $decodedData['hydra:totalItems'] ?? 0;

            return ['items' => $items, 'totalItems' => $totalItems];
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return ['items' => [], 'totalItems' => 0];
        }
    }

    private function fetchResource(string $endpoint, string $type)
    {
        try {
            $response = $this->client->request('GET', $endpoint);

            return $this->serializer->deserialize($response->getContent(), $type, 'json');
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return null;
        }
    }

    private function fetchResources(string $endpoint, array $query, string $type): array
    {
        try {
            $response = $this->client->request('GET', $endpoint, ['query' => $query]);
            $decodedData = $response->toArray();

            return $this->serializer->deserialize(json_encode($decodedData['hydra:member'], JSON_THROW_ON_ERROR), $type.'[]', 'json');
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return [];
        }
    }
}
