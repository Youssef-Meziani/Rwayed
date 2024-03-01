<?php

// src/Controller/PneuController.php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PneuController extends AbstractController
{
    private ApiPlatformConsumerService $apiService;

    public function __construct(ApiPlatformConsumerService $apiService)
    {
        $this->apiService = $apiService;
    }
    #[Route('/catalogue-pneus', name: 'app_catalogue_pneus')]
    public function index(): Response
    {
        $pneus = $this->apiService->fetchPneus();
        return $this->render('pneu/index.html.twig');
    }
}
