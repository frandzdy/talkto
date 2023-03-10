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
use Symfony\Component\Form\Extension\Core\Type\RangeType;
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
        $user = $options['user'];
        $builder
            ->add(
                'genre',
                ChoiceType::class,
                [
                    'choices' => array_flip(Genre::getAvailableGenres()),
                    'label' => 'Genre *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Genre *'
                        ]
                ]
            );
        if ($user->getStripeStatus() === "active") {
            $builder->add(
                'interestExit',
                ChoiceType::class,
                [
                    'choices' => array_flip(InterestExit::getAvailableInterestExits()),
                    'label' => 'Sorties',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Genre'
                        ],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true
                ]
            )->add(
                'interestSports',
                ChoiceType::class,
                [
                    'choices' => array_flip(InterestSports::getAvailableInterestSports()),
                    'label' => 'Sports',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Sports'
                        ],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true
                ]
            )->add(
                'interestHobbies',
                ChoiceType::class,
                [
                    'choices' => array_flip(InterestHobbies::getAvailableInterestHobbies()),
                    'label' => 'Hobbies',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Hobbies'
                        ],
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true
                ]
            )->add(
                'interestSmoker',
                ChoiceType::class,
                [
                    'choices' => array_flip(Smoker::getAvailableSmokers()),
                    'label' => 'Fumeur *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Fumeur *'
                        ]
                ]
            )->add(
                'interestChildren',
                ChoiceType::class,
                [
                    'choices' => array_flip(Children::getAvailableChildrens()),
                    'label' => 'Enfant(s) *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Enfant(s) *'
                        ]
                ]
            );
        }
        // si on a pas de données gps alors on propose le champ ville
        if (!$user->getLat()) {
            $builder->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Ville *',
                            'maxlength' => 255
                        ],
                    'constraints' =>
                    [
                        new NotBlank(['message' => 'Information requise.'])
                    ]
                ]
            );
        } else {
            $builder->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Ville *',
                            'maxlength' => 255
                        ],
                    'required' => false
                ]
            );
        }
        $builder->add(
            'lastname',
            TextType::class,
            [
                'label' => 'Nom *',
                'label_attr' =>
                    [
                        'class' => 'form-label'
                    ],
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
                    'label' => 'Prénom *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Prénom *',
                            'maxlength' => 255
                        ]
                ]
            )
            ->add(
                'age',
                TextType::class,
                [
                    'label' => 'Age *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Age *'
                        ]
                ]
            )
            ->add(
                'size',
                TextType::class,
                [
                    'label' => 'Taille en cm *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'Taille en cm *'
                        ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'E-mail *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'placeholder' => 'E-mail *',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'pictures',
                FileType::class,
                [
                    'label' => false,
                    'required' => false,
                    'mapped' => false,
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-file-input',
                            'accept' => 'image/*'
                        ],
                    'multiple' => true
                ]
            )->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'À propos',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'form-control',
                            'placeholder' => 'À propos',
                            'style' => 'height: 200px',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'interestDistance',
                RangeType::class,
                [
                    'label' => 'Zone de recherche *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'form-control',
                            'data-profil-target' => 'interestDistance',
                            'min' => 1,
                            'max' => 999
                        ],
                    'required' => false
                ]
            )
            ->add(
                'interestAge',
                RangeType::class,
                [
                    'label' => 'Age à partir de *',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'form-control',
                            'data-profil-target' => 'interestAge',
                            'min' => 18,
                            'max' => 120
                        ],
                    'required' => false
                ]
            );
        if ($user->getStripeStatus() === "active") {
            $builder->add(
                'hideAge',
                CheckboxType::class,
                [
                    'label' => 'Masquer l\'age',
                    'label_attr' =>
                        [
                            'class' => 'custom-control-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-control-input',
                        ],
                ]
            )->add(
                'hideDistance',
                CheckboxType::class,
                [
                    'label' => 'Masquer la distance',
                    'label_attr' =>
                        [
                            'class' => 'custom-control-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-control-input',
                        ],
                ]
            )->add(
                'hideAccount',
                CheckboxType::class,
                [
                    'label' => 'Masquer votre compte',
                    'label_attr' =>
                        [
                            'class' => 'custom-control-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-control-input',
                        ],
                ]
            )->add(
                'hideAccount24',
                CheckboxType::class,
                [
                    'label' => 'Masquer votre compte pendant 24 heures',
                    'label_attr' =>
                        [
                            'class' => 'custom-control-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-control-input',
                        ],
                ]
            );
        }
        $builder->add(
            'smoker',
            ChoiceType::class,
            [
                'choices' => array_flip(Smoker::getAvailableSmokers()),
                'label' => 'Fumeur *',
                'label_attr' =>
                    [
                        'class' => 'form-label'
                    ],
                'attr' =>
                    [
                        'placeholder' => 'Fumeur *'
                    ]
            ]
        )->add(
            'children',
            ChoiceType::class,
            [
                'choices' => array_flip(Children::getAvailableChildrens()),
                'label' => 'Enfant(s) *',
                'label_attr' =>
                    [
                        'class' => 'form-label'
                    ],
                'attr' =>
                    [
                        'placeholder' => 'Enfant(s) *'
                    ]
            ]
        )->add(
            'hasNotificationMatch',
            CheckboxType::class,
            [
                'label' => 'Être averti(e) en cas de match',
                'label_attr' =>
                    [
                        'class' => 'custom-control-label'
                    ],
                'attr' =>
                    [
                        'class' => 'custom-control-input',
                    ],
                'required' => false,
            ]
        )->add(
            'interestGenre',
            ChoiceType::class,
            [
                'choices' => array_flip(Genre::getAvailableGenres()),
                'label' => 'Genre de recherche *',
                'label_attr' =>
                    [
                        'class' => 'form-label'
                    ],
                'attr' =>
                    [
                        'placeholder' => 'Genre de recherche *'
                    ]
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'user' => null
            ]
        );
    }
}
