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

class UserPaymentType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['isOnline']) {
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                        'disabled' => 'disabled',
                    ]
                )->add(
                    'lastname',
                    TextType::class,
                    [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Nom *',
                            'maxlength' => 255,
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
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
                            'disabled' => 'disabled',
                        ],
                    ]
                )
            ;
        } else {
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
                    'plainPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'options' => [
                            'toggle' => true,
                            'hidden_label' => 'Masquer',
                            'visible_label' => 'Afficher',
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
                                'autocomplete' => 'new-password',
                                'maxlength' => 255,
                            ],
                        ],

                        'second_options' => [
                            'label' => 'Confirmer votre mot de passe',
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
                    ]
                )
                ->add(
                    'isGuess',
                    CheckboxType::class,
                    [
                        'label' => 'Créer un compte ?',
                        'label_attr' => [
                            'class' => 'custom-control-label',
                        ],
                        'attr' => [
                            'class' => 'custom-control-input pull-left',
                        ],
                    ]
                )->add(
                    'terms',
                    CheckboxType::class,
                    [
                        'label' => 'Accepter notre <a class="alert-link" target="_blank" title="Condition Générale d\'Utilisation" data-turbo="false" href="'.$this->urlGenerator->generate('front_cgu').'">CGU</a>',
                        'label_html' => true,
                        'label_attr' => [
                            'class' => 'custom-control-label',
                        ],
                        'attr' => [
                            'class' => 'custom-control-input pull-right',
                        ],
                    ]
                )
            ;
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event): void {
            $form = $event->getForm();
            $user = $form->getData();

            /**
             * @var User $user
             */
            if (!$user->getCountry()) {
                $form->get('country')->addError(new FormError('Information requise.'));
            }

            if ($user->getIsGuess() && !$form->get('plainPassword')->getData()) {
                $form->get('plainPassword')->get('first')->addError(new FormError('Information requise.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'isOnline' => null,
            ]
        );
    }
}
