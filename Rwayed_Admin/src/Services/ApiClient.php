<?php

namespace App\Services;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiClient {
    private HttpClientInterface $client;
    private SerializerInterface $serializer;
    private string $apiBaseUrl;

    public function __construct(HttpClientInterface $client, SerializerInterface $serializer, string $apiBaseUrl) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->apiBaseUrl = rtrim($apiBaseUrl, '/');
    }

    public function get(string $entityPath, string $responseClassType, array $options = []): mixed {
        $responseArray = $this->request('GET', $entityPath, $options);
        if (isset($responseArray['error'])) {
            return $responseArray; // Retourne l'erreur si présente
        }

        if (isset($responseArray['hydra:member'])) {
            // Pour une collection d'objets
            return array_map(function ($item) use ($responseClassType) {
                return $this->serializer->deserialize(json_encode($item), $responseClassType, 'json');
            }, $responseArray['hydra:member']);
        } else {
            // Pour un objet unique
            return $this->serializer->deserialize(json_encode($responseArray), $responseClassType, 'json');
        }
    }

    public function post(string $entityPath, object $entity, array $options = []): mixed {
        $options['body'] = $this->serializer->serialize($entity, 'json');
        $response = $this->request('POST', $entityPath, $options);
        if (array_key_exists('error', $response)) {
            return $response; // Retourne l'erreur si présente
        }
        return $this->serializer->deserialize(json_encode($response), get_class($entity), 'json');
    }

    public function put(string $entityPath, object $entity, string $responseClassType, array $options = []): mixed {
        $options['body'] = $this->serializer->serialize($entity, 'json');
        $response = $this->request('PUT', $entityPath, $options);
        if (array_key_exists('error', $response)) {
            return $response; // Retourne l'erreur si présente
        }
        return $this->serializer->deserialize(json_encode($response), $responseClassType, 'json');
    }

    public function delete(string $entityPath, array $options = []): mixed {
        $response = $this->request('DELETE', $entityPath, $options);
        if (array_key_exists('error', $response)) {
            return $response; // Retourne l'erreur si présente
        }
        return $response; // Retourne la réponse directement, généralement pas d'entité à désérialiser
    }

    private function request(string $method, string $uri, array $options = []): array {
        $defaultHeaders = ['Accept' => 'application/ld+json', 'Content-Type' => 'application/ld+json'];
        $options['headers'] = array_merge($defaultHeaders, $options['headers'] ?? []);

        try {
            $response = $this->client->request($method, $this->apiBaseUrl . '/' . ltrim($uri, '/'), $options);
            if ($response->getStatusCode() === Response::HTTP_OK || $response->getStatusCode() === Response::HTTP_CREATED) {
                return json_decode($response->getContent(), true);
            }

            return [
                'error' => true,
                'statusCode' => $response->getStatusCode(),
                'message' => 'Request failed with status ' . $response->getStatusCode(),
                'content' => json_decode($response->getContent(), true) // inclut le contenu de la réponse en cas d'erreur
            ];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'An error occurred while connecting to the API: ' . $e->getMessage()];
        }
    }
}
