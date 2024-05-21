<?php
// src/Form/BillingDetailsType.php

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'First Name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'First Name'],
                'data' => $options['first_name']
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Last Name',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Last Name'],
                'data' => $options['last_name']
            ])
            ->add('company_name', TextType::class, [
                'label' => 'Company Name',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Company Name'],
            ])
            ->add('street_address', TextType::class, [
                'label' => 'Street Address',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Street Address'],
                'data' => $options['street_address']
            ])
            ->add('apartment', TextType::class, [
                'label' => 'Apartment, suite, unit etc.',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Apartment, suite, unit etc.'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Town / City',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Town / City'],
                'data' => $options['city']
            ])
            ->add('postcode', IntegerType::class, [
                'label' => 'Postcode / ZIP',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Postcode / ZIP'],
                'data' => $options['postcode']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Email address'],
                'data' => $options['email']
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone'],
                'data' => $options['phone']
            ])
            ->add('terms', CheckboxType::class, [
                'label' => 'I have read and agree to the website terms and conditions',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'label_attr' => [
                    'class' => 'form-check-label',
                    'for' => 'checkout-terms'
                ]
            ])->add('captcha', Recaptcha3Type::class, [
                'action_name' => 'place_order',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'first_name' => '',
            'last_name' => '',
            'company_name' => '',
            'street_address' => '',
            'apartment' => '',
            'city' => '',
            'postcode' => '',
            'email' => '',
            'phone' => '',
        ]);
    }
}
