<?php


namespace App\Form;

use App\Entity\Pneu;
use App\Enum\SaisonEnum;
use App\Enum\TypeVehiculeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('marque', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Entrez la marque'],
            ])
            ->add('typeVehicule', ChoiceType::class, [
                'choices' => array_combine(TypeVehiculeEnum::getAll(), TypeVehiculeEnum::getAll()),
                'required' => true,
                'placeholder' => 'Select a vehicle type',
                'attr' => ['data-placeholder' => 'Select a vehicle type'],
            ])
            ->add('saison', ChoiceType::class, [
                'choices' => array_combine(SaisonEnum::getAll(), SaisonEnum::getAll()),
                'required' => true,
                'placeholder' => 'Select a season',
                'attr' => ['data-placeholder' => 'Select a season'],
            ])
            ->add('prixUnitaire', NumberType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Enter the unit price'],
            ])
            ->add('quantiteStock', NumberType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'Enter the quantity in stock'],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter a description',
                    'rows' => 6,
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => false, // Ne pas afficher la case à cocher de suppression
                'download_uri' => false, // Ne pas afficher le lien de téléchargement
                'download_label' => false, // Ne pas afficher le label de téléchargement
                'image_uri' => false, // Ne pas afficher l'image existante
            ])
            ->add('caracteristique', EntityType::class, [
                'class' => Caracteristique::class,
                'choice_label' => function (Caracteristique $caracteristique) {
                    return sprintf('%s - Charge: %d, Speed: %s',
                        $caracteristique->getTaille(),
                        $caracteristique->getIndiceCharge(),
                        $caracteristique->getIndiceVitesse()
                    );
                },
                'label' => 'Characteristic',
                'required' => true,
                'placeholder' => 'Select a characteristic',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pneu::class,
        ]);
    }
}
