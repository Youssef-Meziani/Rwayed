<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Entity\Technicien;
use App\Repository\AdherentRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    #[Route('/client', name: 'client')]
    public function client(Request $request, AdherentRepository $adherentRepository): Response
    {
        // Récupérer tous les adhérents
        $adherents = $adherentRepository->findAll();
        $totalAdherents = $adherentRepository->count([]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('user/partials/adherents_list.html.twig', [
                    'adherents' => $adherents,
                    'totalAdherents' => $totalAdherents,
                ]),
                'totalAdherents' => $totalAdherents,
            ]);
        }

        return $this->render('user/list.twig', [
            'adherents' => $adherents,
            'totalAdherents' => $totalAdherents,
        ]);
    }

    #[Route('/adherent/toggle/{id}', name: 'adherent_toggle', methods: ['POST'])]
    public function toggleAdherent(Adherent $adherent, EntityManagerInterface $em): JsonResponse
    {
        $adherent->toggleDesactive();
        $em->flush();

        return new JsonResponse([
            'status' => $adherent->isDesactive() ? 'deactivated' : 'activated',
            'message' => $adherent->isDesactive() ? 'Adherent deactivated successfully.' : 'Adherent activated successfully.'
        ]);
    }

    #[Route('/technicians', name: 'technician')]
    public function listTechnicians(Request $request, TechnicienRepository $technicienRepository): Response
    {
        // Récupérer tous les adhérents
        $technicians = $technicienRepository->findAll();
        $totalTechnicians = $technicienRepository->count([]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('user/partials/technicians_list.html.twig', [
                    'technicians' => $technicians,
                    'totalTechnicians' => $totalTechnicians,
                ])
            ]);
        }

        return $this->render('user/grid.twig', [
            'technicians' => $technicians,
            'totalTechnicians' => $totalTechnicians,
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/technician/change-status/{id}', name: 'technician_change_status', methods: ['POST'])]
    public function changeStatus(Technicien $technician, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['status'])) {
            return new JsonResponse(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        $technician->setStatus($data['status']);
        $em->flush();

        return new JsonResponse(['success' => 'Status updated successfully']);
    }

    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        if ($this->security->getUser()->isIsSuper()) {
            return $this->render('user/grid.twig', [
                'title' => 'Admins',
            ]);
        } else {
            throw new NotFoundHttpException();
        }

    }
}
