<?php

namespace App\Services;


use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\RedirectionException;

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

        // return $this->serializer->deserialize($pneusDataJson, 'App\Entity\Pneu[]', 'json');
        return $this->serializer->deserialize($pneusDataJson, 'App\DTO\PneuDTO[]', 'json');
    }

    public function fetchPneuById(int $id)
    {
        try {
            $response = $this->client->request('GET', 'pneus/' . $id);

            if ($response->getStatusCode() !== 200) {
                // Gérer l'erreur ou retourner null / une exception personnalisée
                return null;
            }

            $data = $response->getContent();
            // dd($this->serializer->deserialize($data, 'App\DTO\PneuDTO', 'json'));
            // Pas besoin de décoder puis encoder à nouveau en JSON, désérialisez directement
            return $this->serializer->deserialize($data, 'App\DTO\PneuDTO', 'json');
        } catch (TransportExceptionInterface | ClientException | ServerException | RedirectionException $e) {
            // Log l'erreur ou gérer selon les besoins
            return null;
        }
    }
}
