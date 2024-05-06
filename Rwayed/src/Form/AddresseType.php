<?php
namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
class AddresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, [
                'label' => 'City',
                'constraints' => [
                    new NotBlank(['message' => 'The city field cannot be empty']),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'The city cannot exceed {{ limit }} characters'
                    ])
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Street',
                'constraints' => [
                    new NotBlank(['message' => 'The street field cannot be empty']),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'The street cannot exceed {{ limit }} characters'
                    ])
                ],
            ])
            ->add('postcode', IntegerType::class, [
                'label' => 'Postcode',
                'constraints' => [
                    new NotBlank(['message' => 'The postal code field cannot be empty']),
                ],
            ])
            ->add('setasmydefaultaddress', CheckboxType::class, [
                'label' => 'Set as my default address',
                'required' => false,
            ]);
          
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}