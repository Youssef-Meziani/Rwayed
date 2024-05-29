<?php
// src/Form/ProfileType.php

namespace App\Form;

use App\Entity\Adherent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'First name should not be blank.',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'First name must be at least {{ limit }} characters long.',
                        'maxMessage' => 'First name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Last name should not be blank.',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Last name must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email address should not be blank.',
                    ]),
                    new Email([
                        'message' => 'The email {{ value }} is not a valid email.',
                    ]),
                ],
            ])
            ->add('tele', TextType::class, [
                'label' => 'Phone Number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Phone number should not be blank.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 15,
                        'minMessage' => 'Phone number must be at least {{ limit }} characters long.',
                        'maxMessage' => 'Phone number cannot be longer than {{ limit }} characters.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adherent::class,
        ]);
    }
}
