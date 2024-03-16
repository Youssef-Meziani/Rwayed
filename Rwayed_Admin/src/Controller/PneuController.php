<?php

namespace App\Controller;


use App\Entity\Photo;
use App\Entity\Pneu;
use App\Form\PneuType;
use App\Repository\PneuRepository;
use App\Services\PneuManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/pneu')]
class PneuController extends AbstractController
{

    private $uploadsBaseUrl;

    public function __construct($uploadsBaseUrl)
    {
        $this->uploadsBaseUrl = $uploadsBaseUrl;
    }

    #[Route('', name: 'pneu_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PneuManager $pneuManager): Response
    {
        $pneu = new Pneu();
        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pneuManager->createPneu($pneu, $request->files->get('photo_files', []));
            $this->addFlash('success', 'The tire was added successfully.');
            return $this->redirectToRoute('pneu_index');
        }

        return $this->render('pneu/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{slug}', name: 'pneu_delete', methods: ['POST'])]
    public function delete(string $slug, PneuManager $pneuManager): JsonResponse
    {
        return $pneuManager->deletePneu($slug);
    }

    #[Route('/edit/{slug}', name: 'pneu_edit')]
    public function edit(Request $request, Pneu $pneu,PneuManager $pneuManager, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($pneu->getPhotos() as $oldPhoto) {
                $entityManager->remove($oldPhoto); // Supprime l'entité de la base de données
                $pneu->removePhoto($oldPhoto); // Optionnel : supprime la relation entre le pneu et l'ancienne photo
            }
            $entityManager->flush();
            $files = $request->files->get('photo_files');
            if ($files) {
                foreach ($files as $file) {
                    $photo = new Photo();
                    $photo->setImageFile($file);
                    $photo->setPneu($pneu); // Associe la nouvelle photo au pneu
                    $entityManager->persist($photo); // Prépare la nouvelle photo pour la persistance
                }
            }
            $pneuManager->editPneu($pneu);
            $this->addFlash('success', 'The tire has been successfully modified.');
            return $this->redirectToRoute('pneu_index');
        }
        $images = $pneu->getPhotos();
        $photo = $pneu->getImage();
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');
        return $this->render('pneu/edit.html.twig', [
            'pneu' => $pneu,
            'images' => $images,
            'photo' => $photo,
            'uploads_base_url' => $uploadsBaseUrl,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pneus/json', name: 'pneus_json')]
    public function getPneusJson(PneuRepository $pneuRepository): JsonResponse
    {
        $pneus = $pneuRepository->findAll();
        $data = [];
        foreach ($pneus as $pneu) {
            $data[] = [
                'slugPneu' => $pneu->getSlug(),
                'id' => $pneu->getId(),
                'image' => $this->uploadsBaseUrl . $pneu->getImage(),
                'marque' => $pneu->getMarque(),
                'typeVehicule' => $pneu->getTypeVehicule(),
                'saison' => $pneu->getSaison(),
                'prixUnitaire' => $pneu->getPrixUnitaire(),
                'quantiteStock' => $pneu->getQuantiteStock(),
                'description' => $pneu->getDescription(),
                'caracteristique' => $pneu->getTaille().' - Charge: '.$pneu->getIndiceCharge().', Vitesse: '.$pneu->getIndiceVitesse()
            ];
        }
        return new JsonResponse($data);
    }
}
