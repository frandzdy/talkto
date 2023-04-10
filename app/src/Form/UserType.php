<?php

namespace App\Form;

use App\Entity\Children;
use App\Entity\Genre;
use App\Entity\InterestExit;
use App\Entity\InterestHobbies;
use App\Entity\InterestSports;
use App\Entity\Smoker;
use App\Entity\User;
use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use \Symfony\Component\Form\Extension\Core\Type\EmailType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'genre',
                ChoiceType::class,
                [
                    'choices' => array_flip(Genre::getAvailableGenres()),
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Civilité *',
                        ],
                ]
            )
            ->add(
                'phone',
                TextType::class,
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
                    'label' => false,
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
                'description',
                TextareaType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'À propos',
                            'style' => 'height: 200px',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' =>
                        [
                            'label' => 'Mot de passe',
                            'hash_property_path' => 'password'
                        ],
                    'second_options' =>
                        [
                            'label' => 'Confirmez votre mot de passe',
                        ],
                    'mapped' => false,
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
