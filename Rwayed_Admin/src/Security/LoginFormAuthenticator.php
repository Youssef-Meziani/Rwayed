<?php

namespace App\Security;

use App\Services\ReCaptchaService;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
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
    private $recaptchaService;

    public function __construct(RouterInterface $router,ReCaptchaService $reCaptchaService)
    {
        $this->router = $router;
        $this->recaptchaService = $reCaptchaService;
    }

    protected function getLoginUrl(Request $request): string
    {
        // Nom de la route vers votre formulaire de connexion
        return $this->router->generate('app_login');
    }

    public function authenticate(Request $request): Passport
    {
        $allRequestData = $request->request->all();
        $formData = $allRequestData['login_form'] ?? [];
        // Accéder aux valeurs spécifiques
        $email = $formData['email'] ?? '';
        $password = $formData['password'] ?? '';
        $recaptchaResponse = $formData['captcha'] ?? '';
        // Vérifiez du reCAPTCHA
        if (!$this->recaptchaService->validate($recaptchaResponse)) {
            $request->getSession()->set('authentication_error', 'The CAPTCHA was invalid. Please try again.');
        }
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
