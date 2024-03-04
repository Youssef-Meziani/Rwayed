<?php

namespace App\Services;


use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPlatformConsumerService
{
    private HttpClientInterface $client;
    private SerializerInterface $serializer;

    public function __construct(HttpClientInterface $apiPlatformClient, SerializerInterface $serializer)
    {
        $this->client = $apiPlatformClient;
        $this->serializer = $serializer;
    }

    public function fetchPneus(): array
    {
        $response = $this->client->request('GET', 'pneus');

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $data = $response->getContent();
        $decodedData = json_decode($data, true);

        if (!isset($decodedData['hydra:member'])) {
            return [];
        }

        $pneusDataJson = json_encode($decodedData['hydra:member']);

//        return $this->serializer->deserialize($pneusDataJson, 'App\Entity\Pneu[]', 'json');
        return $this->serializer->deserialize($pneusDataJson, 'App\DTO\PneuDTO[]', 'json');
    }


}
