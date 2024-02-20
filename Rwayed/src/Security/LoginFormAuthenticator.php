<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $allRequestData = $request->request->all();
        $loginOrigin = $request->request->get('login_origin', 'default');
        // Stocker l'origine dans la session pour l'utiliser plus tard
        $request->getSession()->set('login_origin', $loginOrigin);
//        dd($loginOrigin);
        $formData = $allRequestData['login_form'] ?? [];
        // Accéder aux valeurs spécifiques
        $email = $formData['email'] ?? '';
        $password = $formData['password'] ?? '';
        $token = $formData['_csrf_token'] ?? '';
        $rememberMe = $formData['remember_me'] ?? '';
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);


        $passport = new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $token),
            ]
        );
        if ($rememberMe) {
            $passport->addBadge(new RememberMeBadge());
        }

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if (in_array('ROLE_TECHNICIEN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('technicien_home'));    # ? a changer la redirection du technicien
        }

        if (in_array('ROLE_ADHERENT', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('home'));
        }
# !!!!   // fixe that  (inhirted roles in security.yml)
//        return new RedirectResponse($this->urlGenerator->generate('default_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $loginOrigin = $request->getSession()->get('login_origin', 'default');
        $request->getSession()->remove('login_origin'); // Nettoyer après usage

        // Rediriger l'utilisateur vers la page appropriée
        if ($loginOrigin === 'login_page_header') {
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
