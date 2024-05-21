<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
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
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $queryBuilder = $adherentRepository->createQueryBuilder('a');
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit);

        $adherents = $queryBuilder->getQuery()->getResult();
        $totalAdherents = $adherentRepository->count([]);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('user/partials/adherents_list.html.twig', [
                    'adherents' => $adherents,
                    'totalAdherents' => $totalAdherents,
                    'currentPage' => $page,
                    'totalPages' => ceil($totalAdherents / $limit),
                    'limit' => $limit,
                ]),
                'totalPages' => ceil($totalAdherents / $limit),
                'currentPage' => $page,
                'limit' => $limit,
                'totalAdherents' => $totalAdherents,
            ]);
        }

        return $this->render('user/list.twig', [
            'adherents' => $adherents,
            'totalAdherents' => $totalAdherents,
            'currentPage' => $page,
            'totalPages' => ceil($totalAdherents / $limit),
            'limit' => $limit,
        ]);
    }

    #[Route('/technician', name: 'technician')]
    public function technician(): Response
    {
        return $this->render('user/grid.twig', [
            'title' => 'Technicians',
        ]);
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
