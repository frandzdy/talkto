<?php

namespace App\Form;

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
use \Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserPaymentType extends AbstractType
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
                    'label_attr' => ['class' => 'form-text text-muted'],
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
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Adresse *',
                            'maxlength' => 255,
                            'autocomplete' => 'address-line1',
                        ]
                ]
            )->add(
                'additionalAddress',
                TextType::class,
                [
                    'label' => 'Adresse complémentaire',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Appartement, étage, etc.',
                            'maxlength' => 255,
                            'autocomplete' => 'address-line2',
                        ],
                    'required' => false
                ]
            )->add(
                'zipCode',
                TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Code postal *',
                            'maxlength' => 5,
                            'autocomplete' => 'postal-code',
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
                            'autocomplete' => 'address-level2',
                            'maxlength' => 255
                        ]
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
                            'autocomplete' => 'email',
                            'maxlength' => 255
                        ]
                ]
            )->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => ['class' => 'form-text text-muted'],
                        'hash_property_path' => 'password',
                        'attr' => [
                            'placeholder' => 'Au moins 8 caractères dont 1 majuscule, 1 chiffre, 1 symbole',
                            'autocomplete' => 'new-password',
                            'maxlength' => 255
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer votre mot de passe',
                        'attr' => ['maxlength' => 255, 'autocomplete' => 'new-password'],
                    ],
                    'mapped' => false,
                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
                    'constraints' =>
                        [
                            new PasswordRequirements()
                        ]
                ]
            )
            ->add('isGuess', CheckboxType::class,
                [
                    'label' => 'Créer un compte ?',
                    'label_attr' =>
                        [
                            'class' => 'custom-control-label'
                        ],
                    'attr' =>
                        [
                            'class' => 'custom-control-input'
                        ],
                ]
            )
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit']);
    }

    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $form->getData();
        /**
         * @var User $user
         */
//        if (!$user->getLastname()) {
//            $form->get('lastname')->addError(new FormError('Information requise.'));
//        }
//        if (!$user->getFirstname()) {
//            $form->get('firstname')->addError(new FormError('Information requise.'));
//        }
//        if (!$user->getAddress()) {
//            $form->get('address')->addError(new FormError('Information requise.'));
//        }
//        if (!$user->getZipCode()) {
//            $form->get('zipCode')->addError(new FormError('Information requise.'));
//        }
//        if (!$user->getCity()) {
//            $form->get('city')->addError(new FormError('Information requise.'));
//        }
        if (!$user->getCountry()) {
            $form->get('country')->addError(new FormError('Information requise.'));
        }
//        if (!$user->getEmail()) {
//            $form->get('email')->addError(new FormError('Information requise.'));
//        }
//        if (!$user->getPhone()) {
//            $form->get('phone')->addError(new FormError('Information requise.'));
//        }
    }
}
