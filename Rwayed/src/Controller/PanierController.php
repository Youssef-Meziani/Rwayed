<?php

namespace App\Controller;

use App\Entity\Pneu;
use App\Order\Factory\PanierFactory;
use App\Order\Factory\PanierFactoryInterface;
use App\Order\Persister\CartPersisterInterface;
use App\Order\Storage\OrderSessionStorage;
use App\Order\Storage\OrderStorageInterface;
use App\Twig\MinioExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController
{
    public function __construct(
        private OrderStorageInterface $orderStorage,
        private EntityManagerInterface $entityManager,
        private PanierFactory $panierFactory,
        private MinioExtension $minioExtension,
        private CartPersisterInterface $cartPersister,
    ) {
    }

    #[Route('/afficherPanier', name: 'afficherPanier')]
    public function afficherPanier(): Response
    {
        $panier = $this->panierFactory->create();

        return $this->render('cart/view.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/fetch', name: 'fetchCart')]
    public function fetchCartAction(Request $request): Response
    {
        return $this->json($this->orderStorage->recuprerPanier());
    }

    #[Route('/create', name: 'fetchCart')]
    public function createCartAction(Request $request): Response
    {
        $commande = $this->panierFactory->create();
        $this->cartPersister->persist($commande);
        return $this->redirectToRoute("home");
    }

    #[Route('/order-success', name: 'order-success')]
    public function orderSuccessAction(Request $request): Response
    {
        $commande =  $request->getSession()->get('commande');
        $defaultAddress =  $request->getSession()->get('defaultAddress');


        $request->getSession()->remove('commande');
        $request->getSession()->remove('defaultAddress');

        $totalSub = $request->query->get('totalSub');
        $totalCredit = $request->query->get('totalCredit');
        $tax = $request->query->get('tax');


        return $this->render('order-success.twig', [
            'order' => $commande,
            'billingAddress' => $defaultAddress,
            'totalSub' => $totalSub,
            'totalCredit' => $totalCredit,
            'tax' => $tax,
        ]);
    }

    #[Route('/addToCart', name: 'addToCart')]
    public function addToCartAction(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {  // Vérifie si la requête est une requête AJAX
            $id = $request->request->get('id');
            $quantity = $request->request->get('quantity');
            $isRepair = $request->request->get('repair', 'false'); // avec une valeur par défaut à false
            $isRepair = filter_var($isRepair, FILTER_VALIDATE_BOOLEAN);
            $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);

            if (!$pneu) {
                return $this->json(['message' => 'Tire not found'], Response::HTTP_NOT_FOUND);
            }

            $this->orderStorage->ajouterAuPanier($pneu, $quantity, $isRepair);

            return $this->json([
                'message' => 'Tire added successfully',
                'totalItems' => count($this->orderStorage->recuprerPanier()->getLines()),
                'prixTotal' => $this->orderStorage->prixTotalPanier(),
                'maxQuantity' => $pneu->getQuantiteStock(),
                'shippingCost' => 0.00,
                'tax' => $this->orderStorage->prixTotalPanier() * 0.2,
                'total' => $this->orderStorage->prixTotalPanier() * 1.2,
                'items' => $this->formatCartItems(),
            ]);
        }

        return $this->redirectToRoute('fetchCart');
    }

    #[Route('/removeLigne', name: 'removeLigne', methods: ['POST'])]
    public function removeLigneAction(Request $request, OrderStorageInterface $orderStorage): Response
    {
        $id = $request->request->get('id');
        $isRepair = filter_var($request->request->get('repair', false), FILTER_VALIDATE_BOOLEAN);

        try {
            $orderStorage->supprimerLignePanier((int) $id, (bool) $isRepair);
            $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);

            return $this->json([
                'message' => 'Tire removed successfully',
                'totalItems' => count($this->orderStorage->recuprerPanier()->getLines()),
                'prixTotal' => $this->orderStorage->prixTotalPanier(),
                'maxQuantity' => $pneu->getQuantiteStock(),
                'shippingCost' => 0.00,
                'tax' => $this->orderStorage->prixTotalPanier() * 0.2,
                'total' => $this->orderStorage->prixTotalPanier() + ($this->orderStorage->prixTotalPanier() * 0.2),  // Total
                'items' => $this->formatCartItems(),
            ]);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to remove item'], Response::HTTP_BAD_REQUEST);
        }
    }

    private function formatCartItems(): array
    {
        return array_map(function ($item) {
            return [
                'id' => $item->getId(),
                'image' => $this->minioExtension->getMinioUrl($item->getImage()),
                'marque' => $item->getMarque(),
                'quantity' => $item->getQuantity(),
                'prix' => $item->getPrix(),
                'totalPrice' => $item->getTotalPrice(),
                'taxAmount' => $item->getTaxAmount(),
                'withRepair' => $item->isWithRepair(),
            ];
        }, $this->orderStorage->recuprerPanier()->getLines());
    }

    #[Route('/getTotal', name: 'Total')]
    public function getTotalAction(Request $request): Response
    {
        return $this->json(['total' => $this->orderStorage->prixTotalPanier()]);
    }
}
