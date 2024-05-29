<?php

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'id' => 'form-name',
                    'class' => 'form-control',
                    'placeholder' => 'Your Name',
                    'minlength' => '3',
                    'maxlength' => '40'
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 40,
                        'minMessage' => 'Your name must have at least {{ limit }} characters',
                        'maxMessage' => 'Your name cannot have more than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'id' => 'form-email',
                    'class' => 'form-control',
                    'placeholder' => 'Email Address',
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
                        'message' => 'The email "{{ value }}" is not a valid email.'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'id' => 'form-subject',
                    'class' => 'form-control',
                    'placeholder' => 'Subject',
                    'minlength' => '10',
                    'maxlength' => '100'
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 50,
                        'minMessage' => 'The subject must have at least {{ limit }} characters',
                        'maxMessage' => 'The subject cannot have more than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'id' => 'form-message',
                    'class' => 'form-control',
                    'rows' => 4,
                    'minlength' => '10',
                    'maxlength' => '255'
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => 'The message must have at least {{ limit }} characters',
                        'maxMessage' => 'The message cannot have more than {{ limit }} characters'
                    ])
                ]
            ])->add('captcha', Recaptcha3Type::class, [
                'action_name' => 'contact',
            ]);
    }
}