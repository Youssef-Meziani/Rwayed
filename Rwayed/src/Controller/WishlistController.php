<?php

namespace App\Controller;

use App\Entity\Pneu;
use App\Entity\Adherent;
use App\Entity\PneuFavList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WishlistController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // /**
    //  * @Route("/wishlist", name="wishlist", methods={"GET"})
    //  */
    #[Route('/wishlist', name: 'wishlist')]
    public function index(UrlGeneratorInterface $urlGenerator): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user instanceof Adherent) {
            // Rediriger les non connecter vers la page login
            // return $this->redirectToRoute('login');
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
    public function addToWishlist(int $id, Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        // Todo WishlistObserverCD  !!!!!!!!!!!!
        $user = $this->getUser();

        if (!$user instanceof Adherent) {
            // Rediriger les non connecter vers la page de connexion
            return new RedirectResponse($urlGenerator->generate('login'));
        }
        // Vérifiez si la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            // throw new BadRequestHttpException('This route is only accessible via AJAX');
            return new JsonResponse(['error' => 'This route is only accessible via AJAX'], 400);
        }

        // Check if the Pneu exists
        $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);
        if (!$pneu) {
            // throw $this->createNotFoundException('The pneu does not exist');
            return new JsonResponse(['error' => 'The pneu does not exist'], 404);
        }

        // Vérifier si le pneu est déjà dans la liste de souhaits de l'utilisateur
        $wishlistEntry = $this->entityManager->getRepository(PneuFavList::class)->findOneBy([
            'adherent' => $user,
            'pneu' => $pneu,
        ]);
        if ($wishlistEntry) {
            return new JsonResponse(['error' => 'The pneu is already in your wishlist'], 409); //409 Conflict ajout
        }

        // Create a new PneuFavList entity
        $pneuFavList = new PneuFavList();
        $pneuFavList->setAdherent($user);
        $pneuFavList->setPneu($pneu);
        $pneuFavList->setDateAjout(new \DateTime());

        // Save the new PneuFavList entity
        $this->entityManager->persist($pneuFavList);
        $this->entityManager->flush();

        // return new RedirectResponse($urlGenerator->generate('wishlist'));
        return new JsonResponse(['success' => true]);
    }

    #[Route('/wishlist/remove/{id}', name: 'wishlist_remove', methods: ['POST'], options: ['expose' => true])]
    public function removeFromWishlist(int $id, Request $request, UrlGeneratorInterface $urlGenerator): Response
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

        // Récupérer le pneu à supprimer de la liste de souhaits
        $pneu = $this->entityManager->getRepository(Pneu::class)->find($id);
        if (!$pneu) {
            return new JsonResponse(['error' => 'The pneu does not exist'], 404);
        }

        // Rechercher l'entrée correspondante dans la liste de souhaits de l'utilisateur
        $wishlistEntry = $this->entityManager->getRepository(PneuFavList::class)->findOneBy([
            'adherent' => $user,
            'pneu' => $pneu,
        ]);

        // Si l'entrée existe, la supprimer
        if ($wishlistEntry) {
            $this->entityManager->remove($wishlistEntry);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'The pneu is not in your wishlist'], 404);
        }
    }
}
