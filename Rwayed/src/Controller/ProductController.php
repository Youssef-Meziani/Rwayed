<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/product-details', name: 'product-details')]
    public function index(): Response
    {
        return $this->render('Adherent/product-details.twig');
    }
}
