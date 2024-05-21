<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuStrategy\PneuTransformationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private ApiPlatformConsumerService $apiService;
    private $pneuTransformationStrategy;

    public function __construct(ApiPlatformConsumerService $apiService, PneuTransformationStrategy $pneuTransformationStrategy)
    {
        $this->apiService = $apiService;
        $this->pneuTransformationStrategy = $pneuTransformationStrategy;
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $tireDTOs = $this->apiService->fetchPneus(1,16);
        $tires = [];
        foreach ($tireDTOs as $tireDTO) {
            $tires[] = $this->pneuTransformationStrategy->transform($tireDTO);
        }
        return $this->render('index.twig', [
            'tires' => $tires,
        ]);
    }
}
