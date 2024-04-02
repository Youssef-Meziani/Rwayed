<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

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
                    'placeholder' => 'Email Address...',
                    'minlength' => '5',
                    'maxlength' => '50'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'max' => 50,
                        'minMessage' => 'Your email must have at least {{ limit }} characters',
                        'maxMessage' => 'Your email cannot have more than {{ limit }} characters'
                    ]),
                    new Email([
                        'message' => 'The email {{ value }} is not a valid email.'
                    ])
                ]
            ]);
    }
}