<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Entity\Avis;
use App\Entity\Pneu;
use App\Form\AvisType;
use App\Services\ApiPlatformConsumerService;
use App\Strategy\AvisTransformationStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{

    /**
     * @throws \JsonException
     */
    #[Route('/shop/submit-avis/{slug}', name: 'submit_avis', methods: ['POST'])]
    public function submitAvis(Pneu $pneu, Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $formAvis = $this->prepareAvisForm($request, $pneu);

        if ($formAvis->isSubmitted() && $formAvis->isValid()) {
            $this->saveAvis($formAvis, $pneu, $security, $entityManager);
            return $this->json(['message' => 'Your review has been submitted successfully.'], Response::HTTP_OK);
        }

        $errors = $this->getFormErrors($formAvis->getErrors(true));
        return $this->json(['error' => 'There was a problem submitting your review.', 'formErrors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    private function prepareAvisForm(Request $request, Pneu $pneu): FormInterface
    {
        $avis = new Avis();
        $formAvis = $this->createForm(AvisType::class, $avis);
        $formAvis->handleRequest($request);
        return $formAvis;
    }

    private function saveAvis($formAvis, Pneu $pneu, Security $security, EntityManagerInterface $entityManager): void
    {
        $avis = $formAvis->getData();
        $user = $security->getUser();

        if ($user instanceof Adherent) {
            $avis->setAdherent($user);
        } else {
            $avis->setAuthor($formAvis->get('author')->getData());
            $avis->setEmail($formAvis->get('email')->getData());
        }
        $avis->setPneu($pneu);
        $entityManager->persist($avis);
        $entityManager->flush();
    }

    private function getFormErrors(FormErrorIterator $errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return $messages;
    }
}