<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Entity\Avis;
use App\Entity\Pneu;
use App\Form\AvisType;
use App\Services\ApiPlatformConsumerService;
use App\Strategy\AvisTransformationStrategy;
use App\Strategy\PneuTransformationStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    public function __construct(
        private ApiPlatformConsumerService $apiService,
        private PneuTransformationStrategy $pneuTransformationStrategy,
        private AvisTransformationStrategy $avisTransformationStrategy
    ) {}

    #[Route('/shop', name: 'shop')]
    public function index(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $this->handleItemsPerPageUpdate($request, $session);
            return $this->redirectToRoute('shop');
        }

        [$tires, $paginationData] = $this->getPneusWithPagination($request, $session);

        return $this->render('shop.twig', array_merge(['tires' => $tires], $paginationData));
    }

    private function handleItemsPerPageUpdate(Request $request, SessionInterface $session): void
    {
        $itemsPerPage = filter_var($request->request->get('itemsPerPage'), FILTER_VALIDATE_INT, [
            "options" => [
                "default" => ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE,
                "min_range" => 1,
            ],
        ]);
        $session->set('itemsPerPage', $itemsPerPage);
    }

    private function getPneusWithPagination(Request $request, SessionInterface $session): array
    {
        $itemsPerPage = $session->get('itemsPerPage', ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE);
        $page = max($request->query->getInt('page', 1), 1);
        $tiresDTOs = $this->apiService->fetchPneus($page, $itemsPerPage);
        $tires = array_map(fn($pneuDTO) => $this->pneuTransformationStrategy->transform($pneuDTO), $tiresDTOs);

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
    #[Route('/shop/{slug}', name: 'product')]
    public function detail(string $slug, Request $request): Response
    {
        $formAvis = $this->prepareAvisForm($request);

        $pneu = $this->getPneuFromSlug($slug);
        $similarPneus = $this->getSimilarPneus();
        $avisPagination = $this->getAvisPagination($slug, $request);

        return $this->render('product.twig', array_merge([
            'pneu' => $pneu,
            'formAvis' => $formAvis->createView(),
            'similarPneus' => $similarPneus,
        ], $avisPagination));
    }

    private function prepareAvisForm(Request $request): FormInterface
    {
        $avis = new Avis();
        $formAvis = $this->createForm(AvisType::class, $avis);
        $formAvis->handleRequest($request);
        return $formAvis;
    }

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
        return array_map(fn($pneuDTO) => $this->pneuTransformationStrategy->transform($pneuDTO), $similarPneusDTO);
    }

    /**
     * @throws \JsonException
     */
    private function getAvisPagination(string $slug, Request $request): array
    {
        $page = max((int)$request->query->get('page', 1), 1);
        $result = $this->apiService->fetchAvisByPneuSlug($slug, $page);

        $avis = array_map(fn($avisDTO) => $this->avisTransformationStrategy->transform($avisDTO), $result['avis']);
        $totalPages = ceil($result['totalAvis'] / ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS);

        return [
            'avisListe' => $avis,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => ApiPlatformConsumerService::DEFAULT_COUNT_ITEMS_PER_PAGE_AVIS,
            'totalAvis' => $result['totalAvis']
        ];
    }

    /**
     * @throws \JsonException
     */
    #[Route('/shop/submit-avis/{slug}', name: 'submit_avis', methods: ['POST'])]
    public function submitAvis(string $slug, Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $pneu = $this->findPneuOrFail($slug, $entityManager);

        $formAvis = $this->prepareAvisForm($request);

        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $this->saveAvis($formAvis, $pneu, $security, $entityManager);
            return $this->json(['message' => 'Your review has been submitted successfully.'], Response::HTTP_OK);
        }

        $errors = $this->getFormErrors($formAvis->getErrors(true));
        return $this->json(['error' => 'There was a problem submitting your review.', 'formErrors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    private function getFormErrors(FormErrorIterator $errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return $messages;
    }

    private function findPneuOrFail(string $slug, EntityManagerInterface $entityManager): Pneu
    {
        $pneu = $entityManager->getRepository(Pneu::class)->findOneBy(['slug' => $slug]);
        if (!$pneu) {
            throw $this->createNotFoundException('The tire does not exist.');
        }
        return $pneu;
    }

    private function saveAvis($formAvis, Pneu $pneu, Security $security, EntityManagerInterface $entityManager): void
    {
        $avis = $formAvis->getData();
        $user = $security->getUser();

        if ($user instanceof Adherent) {
            $avis->setAdherent($user);
        } else {
            $avis->setAuthor($formAvis->get('author')->getData());
            $avis->setEmail($formAvis->get('email')->getData());
        }
        $avis->setPneu($pneu);
        $entityManager->persist($avis);
        $entityManager->flush();
    }
}
