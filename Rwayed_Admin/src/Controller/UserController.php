<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Entity\Technicien;
use App\Repository\AdherentRepository;
use App\Repository\AdminRepository;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
    public function admin(AdminRepository $adminRepository): Response
    {
        $currentAdmin = $this->security->getUser();
        if ($currentAdmin->isIsSuper()) {
            $admins = $adminRepository->findOtherAdmins($currentAdmin->getId());
            return $this->render('user/admins.twig', [
                'admins' => $admins,
            ]);
        } else {
            throw new NotFoundHttpException();
        }

    }

    #[Route('/admin/toggle/{id}', name: 'admin_toggle', methods: ['POST'])]
    public function toggleAdminStatus(int $id, AdminRepository $adminRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $admin = $adminRepository->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], 404);
        }

        $admin->toggleDesactive();
        $entityManager->flush();

        return new JsonResponse(['message' => 'Admin status changed successfully']);
    }

    #[Route('/admin/toggleSuper/{id}', name: 'admin_toggle_super', methods: ['POST'])]
    public function toggleSuperAdminStatus(int $id, AdminRepository $adminRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $admin = $adminRepository->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], 404);
        }

        $admin->setIsSuper(!$admin->isIsSuper());
        $entityManager->flush();

        return new JsonResponse(['message' => 'Admin super status changed successfully']);
    }

    #[Route('/technician/contact/{id}', name: 'technician_contact', methods: ['POST'])]
    public function contactTechnician(Request $request, MailerInterface $mailer, TechnicienRepository $technicienRepository, $id)
    {
        $data = json_decode($request->getContent(), true);

        $technician = $technicienRepository->find($id);

        if (!$technician) {
            return new JsonResponse(['error' => 'Technician not found'], 404);
        }

        $subject = $data['subject'];
        $message = $data['message'];
        $technicianEmail = $technician->getEmail();

        $email = (new Email())
            ->from('rwayed.support@gmail.com')
            ->to($technicianEmail)
            ->subject($subject)
            ->text($message);

        $mailer->send($email);

        return new JsonResponse(['message' => 'Email sent successfully']);
    }

    #[Route('/technician/profile/{id}', name: 'technician_profile', methods: ['GET'])]
    public function profile(Technicien $technician): Response
    {
        return $this->render('user/profil.twig', [
            'technician' => $technician,
        ]);
    }
}
