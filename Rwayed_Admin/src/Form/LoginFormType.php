<?php

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                  new NotBlank([
                      'message' => 'The email should not be blank.',
                  ]),
                    new Email([
                        'message' => 'The email "{{ value }}" is not a valid email.',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter email',
                    'autocomplete' => 'email',
                    'id' => 'inputEmail'
                ],
                'label_attr' => [
                    'class' => 'inputEmail'
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'The password should not be blank.',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter password',
                    'autocomplete' => 'current-password',
                    'id' => 'password-input'
                ],
                'label_attr' => [
                    'class' => 'password-input'
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'login_captcha',
            ])
            ->add('_remember_me', CheckboxType::class, [
                'required' => false,
                'label' => 'Remember me',
                'attr' => [
                    'id' => 'auth-remember-check'
                ],
            ])

            // ? Symfony utilise des tokens CSRF pour prévenir les attaques de type Cross-Site Request Forgery
            ->add('_csrf_token', HiddenType::class, [
                'mapped' => false, // Typically CSRF token is not mapped to a class property
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Define your form default options here if necessary
        ]);
    }
}
