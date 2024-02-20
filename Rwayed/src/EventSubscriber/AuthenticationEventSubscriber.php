<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationEventSubscriber implements EventSubscriberInterface
{
    private $authenticationUtils;
    private $twig;

    public function __construct(AuthenticationUtils $authenticationUtils, \Twig\Environment $twig)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // Récupère l'erreur de connexion s'il y en a une
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier nom d'utilisateur entré
        $lastUsername = $this->authenticationUtils->getLastUsername();

        // Ajoute les informations d'erreur et le dernier nom d'utilisateur comme variables globales Twig
        $this->twig->addGlobal('login_error', $error);
        $this->twig->addGlobal('last_username', $lastUsername);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
