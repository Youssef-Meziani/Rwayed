<?php

namespace App\Form;

use App\Entity\CodePromo;
use App\Enum\CouponStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints as Assert;

class CodePromoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Promo Code',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        ['max' => 10,
                        'maxMessage' => 'Promo Code cannot be longer than 10 characters',]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter promo code']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter description'],
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 200,
                        'maxMessage' => 'Description cannot be longer than 200 characters',
                    ])
                ],
            ])
            ->add('pourcentage', IntegerType::class, [
                'label' => 'Discount Percentage',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter discount percentage'],
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Percentage must be between {{ min }} and {{ max }}',
                    ])
                ],
            ])
            ->add('dateExpiration', DateTimeType::class, [
                'label' => 'Expiration Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ],
                'constraints' => [
                    new Assert\GreaterThan([
                        'value' => new \DateTime(),
                        'message' => 'The expiration date must be in the future',
                    ]),
                ],
            ])
            ->add('status', EnumType::class, [ // PHP 8.1
                'label' => 'Status',
                'class' => CouponStatus::class, // Specify the class of the enum
                'choice_label' => function(CouponStatus $status) {
                    return ucfirst($status->name); // Using the enum name for display
                },
                'placeholder' => 'Choose a status',
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CodePromo::class,
        ]);
    }
}
