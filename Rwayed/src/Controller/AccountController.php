<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    //todo: add other methods
}