<?php

namespace App\Controller;


use App\Entity\Pneu;
use App\Form\PneuType;
use App\Repository\PneuRepository;
use App\Services\PneuManager;
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
//    #[Route('/new', name: 'pneu_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
//    {
//        $pneu = new Pneu();
//        $form = $this->createForm(PneuType::class, $pneu);
//        $form->handleRequest($request);
//
//        $pneuAddedSuccessfully = false;
//        if ($form->isSubmitted() && $form->isValid()) {
//            $photoFiles = $request->files->get('photo_files');
//            // Vérifiez s'il y a plus de 5 fichiers téléchargés
//            if ($photoFiles && count($photoFiles) > 5) {
//                // Ajoutez une erreur de validation
//                $form->get('photo_files')->addError(new FormError('Vous ne pouvez télécharger que 5 images au maximum.'));
//            } else {
//                // Traitez les fichiers téléchargés et persistez-les
//                foreach ($photoFiles as $file) {
//                    $photo = new Photo();
//                    $photo->setImageFile($file);
//                    $photo->setPneu($pneu);
//                    $entityManager->persist($photo);
//                }
//
//                $caracteristique = $entityManager->getRepository(Caracteristique::class)->find($pneu->getCaracteristique());
//                $pneu->setCaracteristique($caracteristique);
//                // Persistez l'entité Pneu
//                $entityManager->persist($pneu);
//                $entityManager->flush();
//                $pneuAddedSuccessfully = true;
//
//                $pneu = new Pneu();
//                $form = $this->createForm(PneuType::class, $pneu);
//            }
//        }
//
//        // Récupérez les erreurs de validation
//        $errors = $validator->validate($pneu);
//        $errorMessages = [];
//        foreach ($errors as $error) {
//            $errorMessages[] = $error->getMessage();
//        }
//
//        return $this->render('pneu/edit.html.twig', [
//            'pneu' => $pneu,
//            'form' => $form->createView(),
//            'errors' => $errorMessages,
//            'pneuAddedSuccessfully' => $pneuAddedSuccessfully
//        ]);
//    }

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
    public function edit(Request $request, Pneu $pneu,PneuManager $pneuManager): Response
    {
        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                'caracteristique' => $pneu->getCaracteristique()->getTaille().' - Charge: '.$pneu->getCaracteristique()->getIndiceCharge().', Vitesse: '.$pneu->getCaracteristique()->getIndiceVitesse()
            ];
        }
        return new JsonResponse($data);
    }
}
