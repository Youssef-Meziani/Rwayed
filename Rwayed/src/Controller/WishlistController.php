<?php

namespace App\Controller;

use App\Entity\Pneu;
use App\Entity\Adherent;
use App\Entity\PneuFavList;
use App\Services\WishlistService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PneuFavListRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class WishlistController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private $wishlistService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, WishlistService $wishlistService)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->wishlistService = $wishlistService;
    }

    #[Route('/wishlist', name: 'wishlist')]
    public function index(UrlGeneratorInterface $urlGenerator): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // gestion ROLE_ADHERENT dans security.yaml
        if (!$user instanceof Adherent) {
            // Rediriger les non connecter vers la page login
            return new RedirectResponse($urlGenerator->generate('login'));
        }
        // Récupérer la liste de souhaits de l'utilisateur actuellement connecté depuis la base de données
        $wishlist = $this->entityManager->getRepository(PneuFavList::class)->findBy([
            'adherent' => $user,
        ]);

        return $this->render('wishlist.twig', [
            'wishlist' => $wishlist,
        ]);
    }

    #[Route('/wishlist/add/{id}', name: 'wishlist_add', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function addToWishlist(Pneu $pneu, Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Adherent) {
            // Rediriger les non connectés vers la page de connexion
            return new RedirectResponse($urlGenerator->generate('login'));
        }

        // Vérifiez si la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            // Lancer une exception MethodNotAllowedException si la requête n'est pas AJAX
            throw new MethodNotAllowedException(['POST']);
        }

        // Ajouter le pneu à la liste de souhaits en utilisant le service
        $success = $this->wishlistService->addToWishlist($user, $pneu);

        if ($success) {
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'The pneu is already in your wishlist'], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/wishlist/total', name: 'wishlist_total', methods: ['GET'], options: ['expose' => true])]
    public function getWishlistTotal(PneuFavListRepository $pneuFavListRepository): JsonResponse
    {
        $user = $this->security->getUser();

        if ($user instanceof Adherent) {
            $totalItems = $pneuFavListRepository->count(['adherent' => $user]);
            return new JsonResponse(['totalItems' => $totalItems]);
        }

        return new JsonResponse(['totalItems' => 0]);
    }
    
    #[Route('/wishlist/remove/{id}', name: 'wishlist_remove', methods: ['POST'], options: ['expose' => true])]
    public function removeFromWishlist(Pneu $pneu, Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Adherent) {
            // Rediriger les utilisateurs non connectés vers la page de connexion
            return new RedirectResponse($urlGenerator->generate('login'));
        }

        // Vérifier si la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'This route is only accessible via AJAX'], 400);
        }

        // Utiliser le service pour supprimer le pneu de la liste de souhaits
        $success = $this->wishlistService->removeFromWishlist($user, $pneu);

        if ($success) {
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'The pneu is not in your wishlist'], Response::HTTP_NOT_FOUND);
        }
    }
}
