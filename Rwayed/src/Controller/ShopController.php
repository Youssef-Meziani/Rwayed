<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuTransformationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        $page = max($request->query->getInt('page', 1), 1);
        $itemsPerPage = 16;
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');
        $pneusDTOs = $this->apiService->fetchPneus($page, $itemsPerPage);
        $pneus = [];
        foreach ($pneusDTOs as $pneuDTO) {
            $pneus[] = $this->pneuTransformationStrategy->transform($pneuDTO);
        }
        $totalItems = $this->apiService->getTotalItems();

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('shop.twig', [
            'pneus' => $pneus,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'uploads_base_url' => $uploadsBaseUrl,
        ]);
    }
}
