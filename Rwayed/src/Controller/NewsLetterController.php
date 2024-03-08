<?php

namespace App\Controller;

use App\Entity\Emails;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class NewsLetterController extends AbstractController
{
    #[Route('/news_letter', name: 'news_letter')]
    public function handleNewsletterSubscription(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $email = $request->request->all()['newsletter_subscription']['letter_email'];

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
}
