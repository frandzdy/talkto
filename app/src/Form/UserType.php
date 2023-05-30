<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\Civility;
use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type\EnumType;
use \Symfony\Component\Form\Extension\Core\Type\FileType;
use \Symfony\Component\Form\Extension\Core\Type\PasswordType;
use \Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use \Symfony\Component\Form\Extension\Core\Type\TelType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\EmailType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'genre',
                EnumType::class,
                [
                    'class' => Civility::class,
                    'choice_label' => 'label',
                    'label' => 'Information personnelle',
                    'label_attr' => ['class'=>'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Civilité *',
                        ],
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Téléphone *',
                            'maxlength' => 20
                        ]
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'Adresse',
                    'label_attr' => ['class'=>'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Adresse *',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'zipCode',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Code postal *',
                            'maxlength' => 5
                        ]
                ]
            )->add(
                'city',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Ville *',
                            'maxlength' => 255
                        ]
                ]
            )
            ->add(
                'country',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Pays *',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'lastname',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Nom *',
                            'maxlength' => 255
                        ]
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Prénom *',
                            'maxlength' => 255
                        ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'E-mail *',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'picture',
                FileType::class,
                [
                    'label' => false,
                    'required' => false,
                    'mapped' => false,
                    'attr' =>
                        [
                            'accept' => 'image/*'
                        ]
                ]
            )->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => ['class'=>'form-text text-muted'],
                        'hash_property_path' => 'password',
                        'attr' => ['placeholder' => 'Au moins 8 caractères dont 1 majuscule, 1 chiffre, 1 symbole', 'maxlength' => 255],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer votre mot de passe',
                        'attr' => ['maxlength' => 255],
                    ],
                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class
            ]
        );
    }
}
