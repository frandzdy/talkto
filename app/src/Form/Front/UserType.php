<?php

namespace App\Form\Front;

use App\Entity\Country;
use App\Entity\User;
use App\Enum\Civility;
use App\Validator\Constraints\PasswordRequirements;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

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
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' => [
                        'placeholder' => 'Civilité *',
                    ],
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Téléphone *',
                        'maxlength' => 20,
                    ],
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'Adresse',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' => [
                        'placeholder' => 'Adresse *',
                        'maxlength' => 255,
                        'data-bs-toggle' => 'dropdown',
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'false',
                        'class' => 'dropdown-toggle',
                    ],
                    'row_attr' => [
                        'class' => 'dropdown',
                    ],
                ]
            )->add(
                'additionalAddress',
                TextType::class,
                [
                    'label' => 'Adresse complémentaire',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' => [
                        'placeholder' => 'Appartement, étage, etc.',
                        'maxlength' => 255,
                    ],
                    'required' => false,
                ]
            )->add(
                'zipCode',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Code postal *',
                        'maxlength' => 5,
                    ],
                ]
            )->add(
                'city',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Ville *',
                        'maxlength' => 255,
                    ],
                ]
            )
            ->add(
                'country',
                EntityType::class,
                [
                    'class' => Country::class,
                    'choice_label' => 'label',
                    'label' => 'Pays',
                    'placeholder' => '- Sélectionnez un pays -',
                    'autocomplete' => true,
                ]
            )->add(
                'lastname',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Nom *',
                        'maxlength' => 255,
                    ],
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Prénom *',
                        'maxlength' => 255,
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'E-mail *',
                        'maxlength' => 255,
                    ],
                ]
            )->add(
                'uploadPicture',
                FileType::class,
                [
                    'label' => 'Photo de profile',
                    'required' => false,
                    'attr' => [
                        'accept' => 'image/jpeg, image/jpg',
                        'lang' => 'fr',
                        'data-browse' => 'Votre photo',
                    ],
                ]
            )
        ;
        if ($options['edit']) {
            $builder->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'options' => [
                        'toggle' => true,
                        'hidden_label' => null,
                        'visible_label' => null,
                        'translation_domain' => 'messages',
                    ],
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => [
                            'class' => 'form-text text-muted',
                        ],
                        'hash_property_path' => 'password',
                        'attr' => [
                            'placeholder' => 'Au moins 10 caractères dont 1 majuscule, 1 chiffre, 1 symbole',
                            'maxlength' => 255,
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmez',
                        'attr' => [
                            'maxlength' => 255,
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'mapped' => false,
                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
                    'constraints' => [
                        new PasswordRequirements(),
                    ],
                    'required' => false,
                ]
            );
        } else {
            $builder->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'options' => [
                        'toggle' => true,
                        'hidden_label' => null,
                        'visible_label' => null,
                        'translation_domain' => 'messages',
                    ],
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => ['class' => 'form-text text-muted'],
                        'hash_property_path' => 'password',
                        'attr' => [
                            'placeholder' => 'Au moins 10 caractères dont 1 majuscule, 1 chiffre, 1 symbole',
                            'maxlength' => 255,
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmez',
                        'attr' => ['maxlength' => 255],
                    ],
                    'mapped' => false,
                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
                    'constraints' => [
                        new NotBlank(['message' => 'Information requise.']),
                        new PasswordRequirements(),
                    ],
                ]
            )
                ->add(
                    'terms',
                    CheckboxType::class,
                    [
                        'label' => 'Accepter notre <a class="alert-link" target="_blank" title="Condition Générale d\'Utilisation" data-turbo="false" href="'.$this->urlGenerator->generate('front_cgu').'">CGU</a>',
                        'label_html' => true,
                    ]
                )
            ;
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event): void {
            $form = $event->getForm();
            $user = $event->getData();
            if (!$user->getPicture() && !$form->get('uploadPicture')->getData()) {
                $form->get('uploadPicture')->addError(new FormError('Information requise.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'edit' => null,
            ]
        );
    }
}
