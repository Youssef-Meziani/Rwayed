<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Enum\PanierStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route('/update-order-status/{id}', name: 'update_order_status', methods: ['POST'])]
    public function updateOrderStatus(Request $request, Commande $commande, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $newStatus = $data['status'] ?? null;

        if (!PanierStatus::isValid($newStatus)) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        $currentStatus = $commande->getStatutsCommande();

        // If the current status is "Shipped" or "Delivered", do not allow setting to "Canceled"
        if (($currentStatus === PanierStatus::SHIPPED->value || $currentStatus === PanierStatus::DELIVERED->value) && $newStatus === PanierStatus::CANCELED->value) {
            return new JsonResponse(['success' => false, 'message' => 'Cannot cancel an order that has been shipped or delivered'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $commande->setStatutsCommande($newStatus);
            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'message' => 'Failed to update status'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
