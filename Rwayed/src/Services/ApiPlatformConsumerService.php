<?php

namespace App\Services;

use App\DTO\AvisDTO;
use App\DTO\PneuDTO;
use App\Entity\Adherent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
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

    public function __construct(HttpClientInterface $apiPlatformClient, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->client = $apiPlatformClient;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    public function fetchPneus(int $page = 1, int $itemsPerPage = self::DEFAULT_COUNT_ITEMS_PER_PAGE): array
    {
        return $this->fetchResources('pneus', [
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
        ], PneuDTO::class);

        // $response = $this->client->request('GET', 'pneus', [
        //     'query' => [
        //         'page' => $page,
        //         'itemsPerPage' => $itemsPerPage,
        //     ]
        // ]);

        // if ($response->getStatusCode() !== 200) {
        //     return [];
        // }

        // $data = $response->getContent();        
        // $decodedData = json_decode($data, true);

        // if (!isset($decodedData['hydra:member'])) {
        //     return [];
        // }

        // $pneusDataJson = json_encode($decodedData['hydra:member']);

        // // return $this->serializer->deserialize($pneusDataJson, 'App\Entity\Pneu[]', 'json');
        // $similarPneus = $this->serializer->deserialize($pneusDataJson, 'App\DTO\PneuDTO[]', 'json');

        // // Mélangez les pneus similaires
        // shuffle($similarPneus);

        // return $similarPneus;
    }

    public function fetchPneuById(int $id): ?PneuDTO
    {
        return $this->fetchResource('pneus/' . $id, PneuDTO::class);
    }

    /**
     * @throws \JsonException
     */
    public function fetchPneuBySlug(string $slug): ?PneuDTO
    {
        $response = $this->fetchResources('pneus', ['slug' => $slug], PneuDTO::class);
        return $response ? $response[0] : null;
    }

    public function getTotalItems(): int
    {
        try {
            $response = $this->client->request('GET', 'pneus');
            $decodedData = $response->toArray();
            return $decodedData['hydra:totalItems'] ?? 0;
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
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
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return ['items' => [], 'totalItems' => 0];
        }
    }

    private function fetchResource(string $endpoint, string $type)
    {
        try {
            $response = $this->client->request('GET', $endpoint);
            return $this->serializer->deserialize($response->getContent(), $type, 'json');
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return null;
        }
    }

    private function fetchResources(string $endpoint, array $query, string $type): array
    {
        try {
            $response = $this->client->request('GET', $endpoint, ['query' => $query]);
            $decodedData = $response->toArray();
            return $this->serializer->deserialize(json_encode($decodedData['hydra:member'], JSON_THROW_ON_ERROR), $type . '[]', 'json');
        } catch (ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return [];
        }
    }
}
