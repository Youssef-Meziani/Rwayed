<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CompteController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail  = $authenticationUtils->getLastUsername();
        if ($this->getUser()) {
            if (!$this->isGranted('admin')) {
                $error = 'Accès refusé. Vous n\'êtes pas administrateur.';
            } else {
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('compte/login.twig', ['lastEmail' => $lastEmail, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // Le code n'est jamais exécuté, Symfony redirigera vers la page de login
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
