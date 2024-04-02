<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function client(): Response
    {
        return $this->render('user/list.twig');
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
