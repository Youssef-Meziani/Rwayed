<?php

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coupon_code', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'Coupon Code',
                ],
            ])
            ->add('apply', SubmitType::class, [
                'label' => 'Apply Coupon',
                'attr' => [
                    'class' => 'btn btn-sm btn-primary',
                ],
            ])->add('captcha', Recaptcha3Type::class, [
                'action_name' => 'add_coupon',
            ]);
    }
}

