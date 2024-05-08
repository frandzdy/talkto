<?php

namespace App\Form\Front;

use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
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
                            'autocomplete' => 'new-password',
                            'maxlength' => 255,
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
                        new Length(max: 4065),
                        new NotBlank(message: 'Veuillez entrer un mot de passe'),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
