<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\PneuRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class HomeController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/home', name: 'home')]
    public function index(PneuRepository $pneuRepository,AdherentRepository $adherentRepository,LigneCommandeRepository $ligneCommandeRepository): Response
    {
        $mostSoldPneus = $pneuRepository->findMostSoldPneus();
        $seasonalData  = $pneuRepository->countPneusBySeason();
        $adherents = $adherentRepository->findTopAdherentsByPoints();
        $ligneCommandes = $ligneCommandeRepository->findAllWithAdherent();
        return $this->render('index.twig', [
            'mostSoldPneus' => $mostSoldPneus,
            'seasonalData' => $seasonalData,
            'adherents' => $adherents,
            'ligneCommandes' => $ligneCommandes,
        ]);
    }
}
