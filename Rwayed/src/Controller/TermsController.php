<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TermsController extends AbstractController
{
    #[Route('/terms', name: 'terms')]
    public function index(): Response
    {
        return $this->render('terms.twig', [
            'last_modified' => filemtime($this->getParameter('kernel.project_dir') . '/templates/terms.twig'),
        ]);
    }
}
