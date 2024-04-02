<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'invoice')]
    public function index(): Response
    {
        return $this->render('invoice/index.twig');
    }
}
