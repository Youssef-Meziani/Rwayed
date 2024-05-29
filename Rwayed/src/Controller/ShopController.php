<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Pneu;
use App\Form\AvisType;
use App\Repository\LigneCommandeRepository;
use App\Repository\PneuRepository;
use App\Services\ApiPlatformConsumerService;
use App\Services\Session\SessionManager;
use App\Strategy\PneuStrategy\AvisTransformationStrategy;
use App\Strategy\PneuStrategy\PneuTransformationStrategy;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    public function __construct(
        private ApiPlatformConsumerService $apiService,
        private PneuTransformationStrategy $pneuTransformationStrategy,
        private AvisTransformationStrategy $avisTransformationStrategy,
        private SessionManager $sessionManager
    ) {
    }

    private function countPneusByRating(array $pneus): array
    {
        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($pneus as $pneu) {
            $rating = (int) round($pneu->getNoteMoyenne(), 0, PHP_ROUND_HALF_UP);
            if ($rating >= 1 && $rating <= 5) {
                $ratingCounts[$rating]++;
            }
        }

        return $ratingCounts;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws InvalidArgumentException
     */
    #[Route('/shop', name: 'shop')]
    public function index(Request $request, SessionInterface $session,  PneuRepository $pneuRepository): Response
    {
        $session->remove("searchCriteria");
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->handleItemsPerPageUpdate($request, $session);

            return $this->redirectToRoute('shop');
        }

        $priceRange = $request->query->get('price_range');
        $saison = $request->query->get('season');
        $noteMoyenne = $request->query->get('rating');

        $pneusCountBySeason = $pneuRepository->countPneusBySeason();
        // Récupération des pneus et calcul des ratings
        $pneus = $pneuRepository->findAllPneusWithRatings();
        $pneusCountByRating = $this->countPneusByRating($pneus);

        [$tires, $paginationData] = $this->getPneusWithPagination($request, $session);

        return $this->render('shop.twig', array_merge(
            [
                'tires' => $tires,
                'pneusCountBySeason' => $pneusCountBySeason,
                'pneusCountByRating' => $pneusCountByRating,
                'selectedPriceRange' => $priceRange,
                'selectedSeason' => $saison,
                'selectedRating' => $noteMoyenne
            ],
            $paginationData
        ));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws \JsonException
     */
    #[Route('/search', name: 'search')]
    public function search(Request $request, SessionInterface $session,  PneuRepository $pneuRepository): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->handleItemsPerPageUpdate($request, $session);

            return $this->redirectToRoute('shop');
        }
        $priceRange = $request->query->get('price_range');
        $saison = $request->query->get('season');
        $noteMoyenne = $request->query->get('rating');
        $criteria = [
            "prix" => $priceRange ?? null,
            "saison" => $saison ?? null,
            "noteMoyenne" => $noteMoyenne ?? null,
        ];
        $searchCriteria = $this->sessionManager->fillInTheSession($criteria);
        $session->set("searchCriteria", $searchCriteria);
        $pneusCountBySeason = $pneuRepository->countPneusBySeason();

        $pneus = $pneuRepository->findAllPneusWithRatings();
        $pneusCountByRating = $this->countPneusByRating($pneus);

        [$tires, $paginationData] = $this->searchPneusWithPagination($searchCriteria,$request, $session);


        return $this->render('shop.twig', array_merge(
            [
                'tires' => $tires,
                'pneusCountBySeason' => $pneusCountBySeason,
                'pneusCountByRating' => $pneusCountByRating,
                'selectedPriceRange' => $priceRange,
                'selectedSeason' => $saison,
                'selectedRating' => $noteMoyenne
            ],
            $paginationData
        ));
    }

    private function handleItemsPerPageUpdate(Request $request, SessionInterface $session): void
    {
        $itemsPerPage = filter_var($request->request->get('itemsPerPage'), FILTER_VALIDATE_INT, [
            'options' => [
                'default' => ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE,
                'min_range' => 1,
            ],
        ]);
        $session->set('itemsPerPage', $itemsPerPage);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getPneusWithPagination(Request $request, SessionInterface $session): array
    {
        $itemsPerPage = $session->get('itemsPerPage', ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE);
        $page = max($request->query->getInt('page', 1), 1);
        $tiresDTOs = $this->apiService->fetchPneus($page, $itemsPerPage);
        $tires = array_map(fn ($pneuDTO) => $this->pneuTransformationStrategy->transform($pneuDTO), $tiresDTOs);

        $totalItems = $this->apiService->getTotalItems();
        $totalPages = ceil($totalItems / $itemsPerPage);

        return [$tires, [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
        ]];
    }


    /**
     * @throws \JsonException
     */
    private function searchPneusWithPagination(array $searchCriteria, Request $request, SessionInterface $session): array
    {
        $itemsPerPage = $session->get('itemsPerPage', ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE);
        $page = max($request->query->getInt('page', 1), 1);
        $tiresDTOs = $this->apiService->searchBy($searchCriteria,$page, $itemsPerPage);
        $tires = array_map(fn ($pneuDTO) => $this->pneuTransformationStrategy->transform($pneuDTO), $tiresDTOs);

        $totalItems = $this->apiService->getTotalItemsInSearch($searchCriteria);
        $totalPages = ceil($totalItems / $itemsPerPage);

        return [$tires, [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
        ]];
    }


    /**
     * @throws \JsonException
     */
    #[Route('/shop/{slug}', name: 'product')]
    public function detail(string $slug, Request $request, LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $formAvis = $this->prepareAvisForm($request);

        $pneu = $this->getPneuFromSlug($slug);
        $similarPneus = $this->getSimilarPneus();
        $orderCount = $ligneCommandeRepository->countOrdersForPneu($slug);
        $avisPagination = $this->getAvisPagination($slug, $request);

        return $this->render('product.twig', array_merge([
            'pneu' => $pneu,
            'formAvis' => $formAvis->createView(),
            'similarPneus' => $similarPneus,
            'orderCount' => $orderCount,
            'brand' => explode(' ', $pneu->getMarque())[0],
        ], $avisPagination));
    }


    private function prepareAvisForm(Request $request): FormInterface
    {
        $avis = new Avis();
        $formAvis = $this->createForm(AvisType::class, $avis);
        $formAvis->handleRequest($request);

        return $formAvis;
    }

    /**
     * @throws \JsonException
     * @throws InvalidArgumentException
     */
    private function getPneuFromSlug(string $slug): Pneu
    {
        $pneuDTO = $this->apiService->fetchPneuBySlug($slug);
        if (!$pneuDTO) {
            throw $this->createNotFoundException('Le pneu demandé n\'existe pas.');
        }

        return $this->pneuTransformationStrategy->transform($pneuDTO);
    }

    private function getSimilarPneus(): array
    {
        $similarPneusDTO = $this->apiService->fetchPneus(1, 10);

        return array_map(fn ($pneuDTO) => $this->pneuTransformationStrategy->transform($pneuDTO), $similarPneusDTO);
    }

    /**
     * @throws \JsonException
     */
    private function getAvisPagination(string $slug, Request $request): array
    {
        $page = max((int) $request->query->get('page', 1), 1);
        $result = $this->apiService->fetchAvisByPneuSlug($slug, $page);
        $avis = array_map(fn ($avisDTO) => $this->avisTransformationStrategy->transform($avisDTO), $result['avis']);
        $totalPages = ceil($result['totalAvis'] / ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS);

        return [
            'avisListe' => $avis,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS,
            'totalAvis' => $result['totalAvis'],
        ];
    }
}
