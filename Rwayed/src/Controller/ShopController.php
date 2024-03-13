<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuTransformationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    private ApiPlatformConsumerService $apiService;
    private $pneuTransformationStrategy;

    public function __construct(ApiPlatformConsumerService $apiService, PneuTransformationStrategy $pneuTransformationStrategy)
    {
        $this->apiService = $apiService;
        $this->pneuTransformationStrategy = $pneuTransformationStrategy;
    }

    #[Route('/shop', name: 'shop')]
    public function index(): Response
    {
        // minio
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');

        $pneusDTOs = $this->apiService->fetchPneus();
        $pneus = [];
        foreach ($pneusDTOs as $pneuDTO) {
            $pneus[] = $this->pneuTransformationStrategy->transform($pneuDTO);
        }
        // dd($pneusDTOs);
        
        return $this->render('shop.twig', [
            'pneus' => $pneus,
            'uploads_base_url' => $uploadsBaseUrl,
        ]);
    }
}
