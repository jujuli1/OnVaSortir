<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Constraints\Length;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\Length(
                        min: 2,
                        max: 255,
                        minMessage: 'Le nom n est pas assez long'
                    ),
                    new Assert\NotBlank(message: 'Le nom ne peut pas être vide ')
                ],
            ])
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class,[
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])
            ->add('motDePasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new Assert\NotBlank(message: 'mot de passe obligatoire.'),
                        new Assert\Length(
                            min: 8,
                            max: 4096,
                            minMessage: 'Votre mot de passe n’est pas assez long'
                        ),
                        new Assert\Regex(
                            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                            message: 'Le mot de passe doit contenir au moins 8 caracteres, une majuscule, une minuscule, un chiffre, et un caractere spécial '
                        ),
                    ],],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',

                    ],



            ])
            ->add('campus', EntityType::class, [ //relation campus
                'class' => Campus::class,
                'choice_label' => 'nom', // nom du campus
                'required' => true
            ])
            ->add('birthday', BirthdayType::class, [
            'required' => false,
            'widget' => 'single_text', // pour un champ date simple
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
