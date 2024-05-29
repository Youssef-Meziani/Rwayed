<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/store-redirect-url', name: 'store_redirect_url', methods: ['POST'], options: ['expose' => true])]
    public function storeRedirectUrl(Request $request): Response
    {
        $url = $request->request->get('url');
        $session = $request->getSession(); // Récupérer la session depuis la requête
        $session->set('redirect_after_login', $url);

        return new Response('URL stored in session');
    }
}
