<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuTransformationStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private ApiPlatformConsumerService $apiService;
    private $pneuTransformationStrategy;

    public function __construct(ApiPlatformConsumerService $apiService, PneuTransformationStrategy $pneuTransformationStrategy)
    {
        $this->apiService = $apiService;
        $this->pneuTransformationStrategy = $pneuTransformationStrategy;
    }
    #[Route('/cart', name: 'cart')]
    public function index(): Response
    {
        $tiresDTO = $this->apiService->fetchPneus(1, 4);
        if (!$tiresDTO) {
            throw $this->createNotFoundException('The requested tire does not exist.');
        }
        $tires=[];
        foreach ($tiresDTO as $tire) {
            $tires[] = $this->pneuTransformationStrategy->transform($tire);
        }
        
        return $this->render('cart.twig', [
            'tires' => $tires,
        ]);
    }
}
