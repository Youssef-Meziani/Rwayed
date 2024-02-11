<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('account/login.twig');
    }

    #[Route(name: 'logout')]
    public function logout(): Response
    {
        //todo: do some shit
        return $this->render('account/login.twig');
    }


}
