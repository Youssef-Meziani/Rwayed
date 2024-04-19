<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Services\EmailService;
use App\Security\EmailVerifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactUsController extends AbstractController
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route('/contact', name: 'contact')]
    public function index(Request $request): Response
    {
        // Create the form
        $form = $this->createForm(ContactFormType::class);

        // Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get form data
            $formData = $form->getData();

            $this->emailService->sendEmail([
                'from_name' => $formData["name"],
                'to' => $_ENV['SUPPORT_EMAIL'],
                'subject' => $formData["subject"],
                'template' => 'email templates/contact.twig',
                'context' => [
                    'message' => $formData["message"],
                    'email_address' => $formData["email"]
                ]
            ]);

            // Add flash message for successful submission
            $this->addFlash('success', 'Your message has been sent successfully.');

            // Redirect to prevent form resubmission
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact-us.twig', [
            'contact_form' => $form->createView(),
        ]);
    }
}
