<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticationFailureHandlerInterface
{
    use TargetPathTrait;

    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    protected function getLoginUrl(Request $request): string
    {
        // Nom de la route vers votre formulaire de connexion
        return $this->router->generate('app_login');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirigez l'utilisateur vers la page d'accueil après une connexion réussie
        return new RedirectResponse($this->router->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        // Stocker un message d'erreur dans la session
        $request->getSession()->set('authentication_error', $exception->getMessage());

        // Rediriger l'utilisateur vers la page de connexion
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
