<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Repository\PersonneRepository;
use App\Security\EmailVerifier;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityController extends AbstractController
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService, private EmailVerifier $emailVerifier, private VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $this->emailService = $emailService;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $last_email = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class, [
            'email' => $last_email,
        ]);

        return $this->render('security/login.twig', ['loginForm' => $form->createView(), 'last_email' => $last_email, 'error' => $error]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/signup', name: 'signup')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Adherent();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $this->emailService->sendEmail([
                'from_name' => 'Rwayed admin',
                'to' => $user->getEmail(),
                'subject' => 'Please confirm your email address',
                'template' => 'email templates/confirm.twig',
                'context' => [
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
                    'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
                    'userId' => $user->getId(),
                ],
            ]);

            $request->getSession()->set('isVerifiedEmailSent', true);

            return $this->redirectToRoute('msg_confirmation_email');
        }

        return $this->render('security/signup.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/signup/email-verification', name: 'msg_confirmation_email')]
    public function msgConfirmationEmail(Request $request): Response
    {
        if (!$request->getSession()->get('isVerifiedEmailSent')) {
            return $this->redirectToRoute('signup');
        }

        return $this->render('security/signup_check_email.twig');
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, PersonneRepository $personneRepository): Response
    {
        $id = $request->query->get('id');
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('app_home');
        }

        $user = $personneRepository->find($id);
        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirectToRoute('app_home');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }
        $request->getSession()->start();
        $request->getSession()->remove('isVerifiedEmailSent');
        $message = 'Your email address has been verified. You can now log in.';
        $this->addFlash('success', $message);
        $this->addFlash('success_header', $message);

        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
