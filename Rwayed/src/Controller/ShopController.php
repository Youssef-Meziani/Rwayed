<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    private ApiPlatformConsumerService $apiService;

    public function __construct(ApiPlatformConsumerService $apiService)
    {
        $this->apiService = $apiService;
    }
    #[Route('/shop', name: 'shop')]
    public function index(): Response
    {
        $pneus = $this->apiService->fetchPneus();
        return $this->render('shop.twig', [
            'pneus' => $pneus,
        ]);
    }
}
