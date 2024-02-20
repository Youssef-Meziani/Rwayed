<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//#[IsGranted("ROLE_ADMIN")]
class AccountController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        return $this->render('account/dashboard.twig');
    }

    #[Route('/addresses', name: 'addresses')]
    public function addresses(): Response
    {
        return $this->render('account/addresses.twig');
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('account/profile.twig');
    }

    #[Route('/acount-orders', name: 'acount-orders')]
    public function acountOrders(): Response
    {
        return $this->render('account/orders.twig');
    }

    //todo: add other methods
}