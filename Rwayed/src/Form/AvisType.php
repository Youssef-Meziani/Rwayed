<?php

namespace App\Form;

use App\Entity\Avis;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Adherent;

class AvisType extends AbstractType
{
    private ?Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices'  => [
                    '5 Stars Rating' => 5,
                    '4 Stars Rating' => 4,
                    '3 Stars Rating' => 3,
                    '2 Stars Rating' => 2,
                    '1 Star Rating'  => 1,
                ],
                'required' => true,
            ]);
            $user = $this->security->getUser();
            if (!$user instanceof Adherent) {
                $builder
                    ->add('author', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ])
                    ->add('email', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Email(),
                        ],
                    ]);
            }
        $builder
            ->add('commentaire', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
            'csrf_protection' => false,
        ]);
    }
}
