<?php

// src/Twig/AppExtension.php

namespace App\Twig;

use App\Form\LoginFormType;
use App\Form\NewsletterSubscriptionType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('login_form', [$this, 'getLoginForm']),
            new TwigFunction('newsletter_form', [$this, 'getNewsletterForm']),
        ];
    }

    public function getLoginForm()
    {
        return $this->formFactory->create(LoginFormType::class)->createView();
    }

    public function getNewsletterForm()
    {
        return $this->formFactory->create(NewsletterSubscriptionType::class)->createView();
    }
}
