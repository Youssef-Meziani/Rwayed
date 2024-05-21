<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
use App\Repository\CommandeRepository;
use App\Repository\TechnicienRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GraphsController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/stats/orders', name: 'stats_orders')]
    public function getOrderStats(CommandeRepository $commandeRepository): JsonResponse
    {
        $data = $commandeRepository->getMonthlyOrderCount();
        return new JsonResponse($data);
    }

    /**
     * @throws Exception
     */
    #[Route('/stats/total-sales', name: 'stats_total_sales')]
    public function getTotalSalesAmount(CommandeRepository $commandeRepository): JsonResponse
    {
        $totalSales = $commandeRepository->getTotalSalesAmount();
        return new JsonResponse(['totalSales' => $totalSales]);
    }

    /**
     * @throws Exception
     */
    #[Route('/stats/total-orders', name: 'stats_total_orders')]
    public function getTotalOrderCount(CommandeRepository $commandeRepository): JsonResponse
    {
        $totalOrders = $commandeRepository->getTotalOrderCount();
        return new JsonResponse(['totalOrders' => $totalOrders]);
    }

    /**
     * @throws Exception
     */
    #[Route('/stats/total-adherents', name: 'stats_total_adherents')]
    public function getTotalAdherentsCount(AdherentRepository $adherentRepository): JsonResponse
    {
        $totalAdherents = $adherentRepository->getTotalAdherentsCount();
        return new JsonResponse(['totalAdherents' => $totalAdherents]);
    }

    /**
     * @throws Exception
     */
    #[Route('/stats/total-techniciens', name: 'stats_total_techniciens')]
    public function getTotalTechniciensCount(TechnicienRepository $technicienRepository): JsonResponse
    {
        $totalTechniciens = $technicienRepository->getTotalTechniciensCount();
        return new JsonResponse(['totalTechniciens' => $totalTechniciens]);
    }
}
