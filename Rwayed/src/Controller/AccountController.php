<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AccountController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_ADHERENT')]
    public function dashboard(): Response
    {
        return $this->render('account/dashboard.twig');
    }

    #[Route('/addresses', name: 'addresses')]
    #[IsGranted('ROLE_ADHERENT')]
    public function addresses(): Response
    {
        return $this->render('account/addresses.twig');
    }

    #[Route('/profile', name: 'profile')]
    #[IsGranted('ROLE_ADHERENT')]
    public function profile(): Response
    {
        return $this->render('account/profile.twig');
    }

    #[Route('/acount-orders', name: 'acount-orders')]
    #[IsGranted('ROLE_ADHERENT')]
    public function acountOrders(): Response
    {
        return $this->render('account/orders.twig');
    }

    #[Route('/change-password', name: 'change-password')]
    #[IsGranted('ROLE_ADHERENT')]
    public function changePassword(): Response
    {
        return $this->render('account/password.twig');
    }

    #[Route('/garage', name: 'garage')]
    public function garage(AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_TECHNICIEN')) {
            throw new NotFoundHttpException();
        }

        return $this->render('account/garage.twig');
    }
}