<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('dateHeure')
            ->add('lieu')

            ->add('status', ChoiceType::class, [
                'label' => 'Statut de la sortie',
                'choices'  => [
                    'Ouvert' => True,
                    'Fermé'  => False,
                ],
                'expanded' => false,
                'multiple' => false,   // choix unique
            ])
            ->add('photo', FileType::class, [
                'label' => 'Illustrez votre sortie avec un photo',
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
            'data_class' => Sortie::class,
        ]);
    }
}
