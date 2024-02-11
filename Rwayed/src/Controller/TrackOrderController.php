<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TrackOrderController extends AbstractController
{
    #[Route('/track-order', name: 'track-order')]
    public function index(): Response
    {
        return $this->render('track-order.twig');
    }
}
