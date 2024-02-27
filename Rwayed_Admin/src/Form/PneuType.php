<?php


namespace App\Form;

use App\Entity\Pneu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Entity\Caracteristique;

class PneuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class)
            ->add('typeVehicule', TextType::class)
            ->add('saison', TextType::class)
            ->add('prixUnitaire', NumberType::class)
            ->add('quantiteStock', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
            ])
            ->add('id_cara', EntityType::class, [
                'class' => Caracteristique::class,
                'choice_label' => function (Caracteristique $caracteristique) {
                    return sprintf('%s - Charge: %d, Vitesse: %s',
                        $caracteristique->getTaille(),
                        $caracteristique->getIndiceCharge(),
                        $caracteristique->getIndiceVitesse()
                    );
                },
                'label' => 'Caractéristique'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pneu::class,
        ]);
    }
}
