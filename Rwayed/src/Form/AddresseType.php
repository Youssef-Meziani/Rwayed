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
class AddresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, [
                'label' => 'City',
                'constraints' => [new NotBlank()],
            ])
            ->add('street', TextType::class, [
                'label' => 'Street',
                'constraints' => [new NotBlank()],
            ])
            ->add('postcode', IntegerType::class, [
                'label' => 'Postcode',
                'constraints' => [new NotBlank()],
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