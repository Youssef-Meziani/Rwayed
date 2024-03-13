<?php

namespace App\Security;

use App\Repository\PersonneRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private const CSRF_TOKEN_ID = 'authenticate';
    private UrlGeneratorInterface $urlGenerator;
    private AuthorizationCheckerInterface $authorizationChecker;
    private PersonneRepository $personneRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authorizationChecker, PersonneRepository $personneRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->personneRepository = $personneRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $allRequestData = $request->request->all();
        $request->getSession()->set('login_origin', $request->request->get('login_origin', 'default'));
        $formData = $allRequestData['login_form'] ?? [];
        $email = $formData['email'] ?? '';
        $password = $formData['password'] ?? '';
        $token = $formData['_csrf_token'] ?? '';
        $rememberMe = isset($formData['remember_me']);

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

//        // Initialiser le tableau de badges avec les badges obligatoires
//        $badges = [
//            new CsrfTokenBadge(self::CSRF_TOKEN_ID, $token),
//        ];
//
//        // Ajouter conditionnellement le badge RememberMe
//        if ($rememberMe) {
//            $badges[] = new RememberMeBadge();
//        }

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                $user = $this->personneRepository->findOneBy(['email' => $userIdentifier]);
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Email could not be found.');
                }

                if (!$user->isVerified()) {
                    throw new CustomUserMessageAuthenticationException('Your account is not verified. Please check your email.');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge(self::CSRF_TOKEN_ID, $token),
                new RememberMeBadge()
            ]
        );
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Exemple de redirection basée sur des rôles avec support pour les rôles hérités
        if ($this->authorizationChecker->isGranted('ROLE_TECHNICIEN')) {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADHERENT')) {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }

        $request->getSession()->getFlashBag()->add('error', 'Your account does not have the necessary permissions to access this resource');
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $loginOrigin = $request->getSession()->get('login_origin', 'default');
        $request->getSession()->remove('login_origin'); // Nettoyer après usage

        // Rediriger l'utilisateur vers la page appropriée
        if ($loginOrigin === 'login_page_header') {
            // Stocker un message d'erreur dans la session pour le form login qui se trouve dans le header
            $request->getSession()->set('authentication_error_header', $exception->getMessage());
            $url = $request->headers->get('referer') ?? $this->urlGenerator->generate('home');
            return new RedirectResponse($url);
        }
        // Stocker un message d'erreur dans la session pour le form login
        $request->getSession()->set('authentication_error', $exception->getMessage());
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
