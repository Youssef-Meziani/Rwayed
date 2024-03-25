<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuTransformationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $itemsPerPage = filter_var($request->request->get('itemsPerPage'), FILTER_VALIDATE_INT, ["options" => ["default" => 16, "min_range" => 1]]);
            $session->set('itemsPerPage', $itemsPerPage);
            return $this->redirectToRoute('shop', [], 303);
        }
        $itemsPerPage = $session->get('itemsPerPage', 16);
        $page = max($request->query->getInt('page', 1), 1);
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');
        $tiresDTOs = $this->apiService->fetchPneus($page, $itemsPerPage);
        $tires = [];
        foreach ($tiresDTOs as $pneuDTO) {
            $tires[] = $this->pneuTransformationStrategy->transform($pneuDTO);
        }
        $totalItems = $this->apiService->getTotalItems();

        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('shop.twig', [
            'tires' => $tires,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'uploads_base_url' => $uploadsBaseUrl,
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/shop/{slug}', name: 'product')]
    public function detail(string $slug): Response
    {
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');
        $pneuDTO = $this->apiService->fetchPneuBySlug($slug);
        if (!$pneuDTO) {
            throw $this->createNotFoundException('Le pneu demandé n\'existe pas.');
        }
        $pneu = $this->pneuTransformationStrategy->transform($pneuDTO);
        // Récupérez une liste de tires similaires
        $similarPneus = $this->apiService->fetchPneus(1,1);
        return $this->render('product.twig', [
            'pneu' => $pneu,
            'similarPneus' => $similarPneus,
            'uploads_base_url' => $uploadsBaseUrl,
        ]);
    }
}
