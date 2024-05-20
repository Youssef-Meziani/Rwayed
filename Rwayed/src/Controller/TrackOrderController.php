<?php

namespace App\Controller;

use App\Form\TrackOrderType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class TrackOrderController extends AbstractController
{
    #[Route('/track-order', name: 'track-order')]
    public function index(Request $request, CommandeRepository $commandeRepository): Response
    {
        if (!$this->getUser()) {
            $session = $request->getSession();
            $session->set('referer_checkout', $request->getUri());
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(TrackOrderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($request->isXmlHttpRequest()) {
                $codeUnique = $data['order_id'];
                $user = $this->getUser();

                $commande = $commandeRepository->findOneBy([
                    'codeUnique' => $codeUnique,
                    'adherent' => $user,
                ]);
                if (!$commande) {
                    return new JsonResponse(['status' => 'error', 'message' => 'Order not found'], Response::HTTP_NOT_FOUND);
                }

                return new JsonResponse(['status' => 'success', 'orderStatus' => $commande->getStatutsCommandeLabel()]);
            }
        }

        return $this->render('track-order.twig', [
            'form' => $form->createView(),
        ]);
    }
}
