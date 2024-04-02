<?php

namespace App\Controller;

use App\Entity\Emails;
use App\Form\NewsletterSubscriptionType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;

class NewsLetterController extends AbstractController
{
    #[Route('/news_letter', name: 'news_letter')]
    public function handleNewsletterSubscription(Request $request, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory): Response {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        // Create an instance of the form
        $form = $formFactory->create(NewsletterSubscriptionType::class);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the submitted data
            $email = $form->get('letter_email')->getData();

            // Try to persist the email
            try {
                $newEmail = new Emails();
                $newEmail->setEmailAddress($email);

                $entityManager->persist($newEmail);
                $entityManager->flush();

                return $this->json(['subscribed' => false]);
            } catch (UniqueConstraintViolationException $e) {
                return $this->json(['subscribed' => true]);
            }
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        // Return only the custom error messages
        return $this->json(['subscribed' => false, 'errors' => $errors]);
    }
}
