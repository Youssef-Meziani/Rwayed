<?php

namespace App\Controller;

use App\Entity\Pneu;
use App\Order\Factory\PanierFactoryInterface;
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
        private PanierFactoryInterface $panierFactory,
        private MinioExtension $minioExtension,
    ) {
    }

    #[Route('/afficherPanier', name: 'afficherPanier')]
    public function afficherPanier()
    {
        $panier = $this->panierFactory->create();
        dd($panier);

        return $this->render('cart/view.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/fetch', name: 'fetchCart')]
    public function fetchCartAction(Request $request)
    {
        dd($this->orderStorage->recuprerPanier());
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
                'tax' => $this->orderStorage->prixTotalPanier() * 0.2,  // Supposons qu'il n'y a pas de taxe
                'total' => $this->orderStorage->prixTotalPanier() + ($this->orderStorage->prixTotalPanier() * 0.2),  // Total
                'items' => array_map(function ($item) {
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
                }, $this->orderStorage->recuprerPanier()->getLines()),
            ]);
        }

        return $this->redirectToRoute('fetchCart');
    }

    #[Route('/remove', name: 'removeCart')]
    public function removeAction(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('panier');

        return $this->redirectToRoute('home');
    }

    #[Route('/removeLigne', name: 'removeLigne', methods: ['POST'])]
    public function removeLigneAction(Request $request, OrderStorageInterface $orderStorage): Response
    {
        $id = $request->request->get('id');
        $isRepair = $request->request->get('repair', 'false');
        $isRepair = filter_var($isRepair, FILTER_VALIDATE_BOOLEAN);

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
                'items' => array_map(function ($item) {
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
                }, $this->orderStorage->recuprerPanier()->getLines()),
            ]);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to remove item'], Response::HTTP_BAD_REQUEST);
        }
    }

    //    #[Route('/removeL', name:'removeL')]
    //    public function removeLAction(Request $request):Response
    //    {
    //        $id = 67;
    //        $isRepair = false;
    //        $this->orderStorage->supprimerLignePanier($id,$isRepair);
    //        return $this->redirectToRoute("fetchCart");
    //    }

    #[Route('/getTotal', name: 'Total')]
    public function getTotalAction(Request $request): Response
    {
        dd($this->orderStorage->prixTotalPanier());
    }
}
