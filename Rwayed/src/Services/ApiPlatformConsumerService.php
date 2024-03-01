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

        // Vérification immédiate du statut de la réponse.
        if ($response->getStatusCode() !== 200) {
            return [];
        }

        // Récupération et décodage du contenu de la réponse.
        $data = $response->getContent();
        $decodedData = json_decode($data, true);

        // Vérification de l'existence de la clé 'hydra:member' dans les données décodées.
        if (!isset($decodedData['hydra:member'])) {
            return [];
        }

        // Ré-encodage des données spécifiques ('hydra:member') en JSON pour la désérialisation.
        $pneusDataJson = json_encode($decodedData['hydra:member']);

        // Désérialisation du JSON ré-encodé en un tableau d'objets Pneu.
//        return $this->serializer->deserialize($pneusDataJson, 'App\Entity\Pneu[]', 'json');
        return $this->serializer->deserialize($pneusDataJson, 'App\DTO\PneuDTO[]', 'json');
    }


}
