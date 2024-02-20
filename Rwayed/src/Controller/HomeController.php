<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('index.twig');
    }
    #[Route('/nettoyer-modal', name:'nettoyer_modal')]
    public function nettoyerModal(Request $request): JsonResponse
    {
        $request->getSession()->remove('show_login_modal');
        $request->getSession()->remove('authentication_error');
        return new JsonResponse(['status' => 'success']);
    }
}
