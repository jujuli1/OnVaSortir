<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class UpdateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('dateHeure')
            ->add('lieu')

            ->add('status', ChoiceType::class, [
                'label' => 'Statut :',
                'choices'  => [
                    'Ouvert' => True,
                    'Fermé'  => False,
                ],
                'expanded' => false,
                'multiple' => false,   // choix unique
            ])
            ->add('photo', FileType::class, [
                'label' => 'Illustrez votre sortie avec une photo : ',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new FileConstraint(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
                        mimeTypesMessage: 'Veuillez télécharger une image valide (JPEG, PNG, GIF)'
                    )
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
