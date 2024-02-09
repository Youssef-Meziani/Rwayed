<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WishlistController extends AbstractController
{
    #[Route('/wishlist', name: 'wishlist')]
    public function index(): Response
    {
        return $this->render('Adherent/wishlist.twig');
    }
}
