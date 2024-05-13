<?php

namespace App\Handlers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class CustomAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse
    {
        // Utilisez la session à partir de l'objet Request
        $session = $request->getSession();
        // Stockez un message d'erreur dans la session pour l'afficher plus tard
        $session->getFlashBag()->add('error', 'No user exists for this session or access.');

        // Redirigez l'utilisateur vers la page de connexion ou toute autre page appropriée
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
