<?php

namespace App\Controller;


use App\Entity\Photo;
use App\Entity\Pneu;
use App\Form\PneuType;
use App\Repository\PneuRepository;
use App\Services\PhotoService;
use App\Services\PneuManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/tire')]
#[IsGranted("ROLE_ADMIN")]
class PneuController extends AbstractController
{


    public function __construct(private $uploadsBaseUrl, private PhotoService $photoService,)
    {}

    #[Route('', name: 'pneu_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PneuManager $pneuManager): Response
    {
        $pneu = new Pneu();
        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $pneuManager->createPneu($pneu, $request->files->get('photo_files', []));
                $this->addFlash('success', 'The tire was added successfully.');
                return new JsonResponse(['success' => true]);
            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[$error->getOrigin()->getName()][] = $error->getMessage();
                }
                return new JsonResponse(['success' => false, 'errors' => $errors]);
            }
        }

        return $this->render('tire/index.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{slug}', name: 'pneu_delete', methods: ['POST'])]
    public function delete(string $slug, PneuManager $pneuManager): JsonResponse
    {
        return $pneuManager->deletePneu($slug);
    }

    #[Route('/edit/{slug}', name: 'pneu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pneu $pneu, PneuManager $pneuManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PneuType::class, $pneu);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Remove old photos
                foreach ($pneu->getPhotos() as $oldPhoto) {
                    $entityManager->remove($oldPhoto);
                    $pneu->removePhoto($oldPhoto);
                }
                $entityManager->flush();

                // Process new photo files
                $photoFiles = $request->files->get('photo_files');
                if (!empty($photoFiles)) {
                    $this->photoService->processPhotoFiles($pneu, $photoFiles);
                }

                // Update tire information
                $pneuManager->editPneu($pneu);
                $this->addFlash('success', 'The tire has been successfully modified.');

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => true]);
                }

                return $this->redirectToRoute('pneu_index');
            } else {
                if ($request->isXmlHttpRequest()) {
                    $errors = [];
                    foreach ($form->getErrors(true) as $error) {
                        $errors[$error->getOrigin()->getName()][] = $error->getMessage();
                    }
                    return new JsonResponse(['success' => false, 'errors' => $errors]);
                }
            }
        }

        $images = $pneu->getPhotos();
        $photo = $pneu->getImage();
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');

        return $this->render('tire/edit.twig', [
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
