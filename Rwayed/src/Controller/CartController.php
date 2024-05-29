<?php

namespace App\Controller;

use App\Coupon\CouponInterface;
use App\Entity\Pneu;
use App\Form\CouponType;
use App\Order\Factory\PanierFactory;
use App\Order\Storage\OrderSessionStorage;
use App\Order\Storage\OrderStorageInterface;
use App\Processor\CartProcessor;
use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuStrategy\PneuTransformationStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(
        private ApiPlatformConsumerService $apiService,
        private PneuTransformationStrategy $pneuTransformationStrategy,
    ) {}
    #[Route('/cart', name: 'cart')]
    public function index(): Response
    {
        $tiresDTO = $this->apiService->fetchPneus(1, 4);
        if (!$tiresDTO) {
            throw $this->createNotFoundException('The requested tire does not exist.');
        }
        $tires=[];
        foreach ($tiresDTO as $pneu) {
            $tires[] = $this->pneuTransformationStrategy->transform($pneu);
        }

        return $this->render('cart.twig', [
            'tires' => $tires,
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/cart/update', name: 'cart_update', methods: ['POST'])]
    public function updateCart(Request $request, OrderSessionStorage $orderSessionStorage, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $id = $data['id'];
        $idPneu = $data['idPneu'];
        $quantity = $data['quantity'];
        $isRepair = filter_var($data['is_repair'], FILTER_VALIDATE_BOOLEAN);

        $item = $entityManager->getRepository(Pneu::class)->find($idPneu);
        if (!$item) {
            return new JsonResponse(['success' => false, 'message' => 'Item not found.']);
        }

        $availableStock = $item->getQuantiteStock();

        if ($quantity > $availableStock) {
            return new JsonResponse([
                'success' => true,
                'maxStock' => $availableStock,
                'message' => 'Quantity exceeds available stock.'
            ]);
        }

        try {
            $orderSessionStorage->modifierLignePanier($id, $quantity);
            return new JsonResponse(['success' => true, 'maxStock' => $availableStock]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }


}
