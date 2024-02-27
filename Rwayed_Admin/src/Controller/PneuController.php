<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Pneu;
use App\Form\PneuType;
use App\Repository\PneuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pneu')]
class PneuController extends AbstractController
{
    #[Route('/new', name: 'pneu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pneu = new Pneu();
        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFiles = $request->files->get('photo_files');

            if ($photoFiles) {
                foreach ($photoFiles as $file) {
                    $photo = new Photo();
                    // Supposons que vous avez une méthode setFile dans votre entité Photo
                    // qui s'occupe de la logique d'upload.
                    $photo->setImageFile($file);
                    $photo->setPneu($pneu); // Associez la photo au pneu
                    $entityManager->persist($photo);
                }
            }
            // Assurez-vous que l'entité Pneu et les entités Photo associées sont correctement configurées
            // pour être persistées avec Doctrine (si nécessaire).

            $entityManager->persist($pneu);
            // Parcourir chaque photo et les persister si nécessaire
            foreach ($pneu->getPhotos() as $photo) {
                $entityManager->persist($photo);
            }
            $entityManager->flush();

            return $this->redirectToRoute('pneu_index');
        }

        return $this->render('pneu/new.html.twig', [
            'pneu' => $pneu,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'pneu_index', methods: ['GET'])]
    public function index(PneuRepository $pneuRepository): Response
    {
        return $this->render('pneu/index.html.twig', [
            'pneus' => $pneuRepository->findAll(),
        ]);
    }
}
