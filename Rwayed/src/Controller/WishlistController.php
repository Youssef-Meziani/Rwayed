<?php

namespace App\Controller;

use App\Entity\Pneu;
use App\Entity\Adherent;
use App\Entity\PneuFavList;
use App\Services\WishlistService;
// use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class WishlistController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    // private PublisherInterface $publisher;
    private $wishlistService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, WishlistService $wishlistService)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        // $this->publisher = $publisher;
        $this->wishlistService = $wishlistService;
    }

    // /**
    //  * @Route("/wishlist", name="wishlist")
    //  */
    #[Route('/wishlist', name: 'wishlist')]
    public function index(UrlGeneratorInterface $urlGenerator): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$this->security->isGranted('ROLE_ADHERENT') || !$user instanceof Adherent) {
            // Rediriger les non connecter vers la page login
            return new RedirectResponse($urlGenerator->generate('login'));
        }
        // Récupérer la liste de souhaits de l'utilisateur actuellement connecté depuis la base de données
        $wishlist = $this->entityManager->getRepository(PneuFavList::class)->findBy([
            'adherent' => $user,
        ]);
        // Calculer le nombre total d'éléments dans la liste de souhaits
        $totalItems = count($wishlist);

        // Publier le nombre total d'éléments via Mercure
        // $this->publisher->__invoke(new Update(
        //     '/wishlist/totalItems',
        //     json_encode(['totalItems' => $totalItems])
        // ));
        
        return $this->render('wishlist.twig', [
            'wishlist' => $wishlist,
            'totalItems' => $totalItems,
        ]);
    }

    #[Route('/wishlist/add/{id}', name: 'wishlist_add', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function addToWishlist(int $id, Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        // Todo WishlistObserverCD  !!!!!!!!!!!!
        $user = $this->getUser();
        
        if (!$this->security->isGranted('ROLE_ADHERENT') || !$user instanceof Adherent) { 
            // Rediriger les non connectés vers la page de connexion
            return new RedirectResponse($urlGenerator->generate('login'));
        }

        // Vérifiez si la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            // Lancer une exception MethodNotAllowedException si la requête n'est pas AJAX
            throw new MethodNotAllowedException(['POST'], 'This route is only accessible via AJAX');
        }

        // Check if the Pneu exists
        $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);
        if (!$pneu) {
            return new JsonResponse(['error' => 'The pneu does not exist'], 404);
        }

        // Ajouter le pneu à la liste de souhaits en utilisant le service
        $success = $this->wishlistService->addToWishlist($user, $pneu);

        if ($success) {
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'The pneu is already in your wishlist'], 409);
        }
    }


    #[Route('/wishlist/remove/{id}', name: 'wishlist_remove', methods: ['POST'], options: ['expose' => true])]
    public function removeFromWishlist(int $id, Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();

        if (!$this->security->isGranted('ROLE_ADHERENT') || !$user instanceof Adherent) {
            // Rediriger les utilisateurs non connectés vers la page de connexion
            return new RedirectResponse($urlGenerator->generate('login'));
        }

        // Vérifier si la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse(['error' => 'This route is only accessible via AJAX'], 400);
        }

        // Récupérer le pneu à supprimer de la liste de souhaits
        $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);
        if (!$pneu) {
            return new JsonResponse(['error' => 'The pneu does not exist'], 404);
        }

        // Utiliser le service pour supprimer le pneu de la liste de souhaits
        $success = $this->wishlistService->removeFromWishlist($user, $pneu);

        if ($success) {
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'The pneu is not in your wishlist'], 404);
        }
    }
}
