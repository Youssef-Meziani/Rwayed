<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class NewsletterSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('letter_email', EmailType::class, [
                'attr' => [
                    'name' => 'email',
                    'id' => 'footer-newsletter-address',
                    'class' => 'footer-newsletter__form-input',
                    'placeholder' => 'Email Address...'
                ]
            ]);
    }
}