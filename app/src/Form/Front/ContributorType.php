<?php

namespace App\Form\Front;

use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Edition d'utilisateurs BO.
 */
class ContributorType extends AbstractType
{
    /**
     * <@inheritDoc>.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'fullname',
                TextType::class,
                [
                    'label' => 'Nom / Prénom',
                    'attr' => ['maxlength' => 255],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => ['maxlength' => 255],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
                    'required' => count($options['validation_groups']) > 1,
                    'options' => [
                        'toggle' => true,
                        'hidden_label' => 'Masquer',
                        'visible_label' => 'Afficher',
                        'translation_domain' => 'messages',
                    ],
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'attr' => [
                            'placeholder' => 'Au moins 8 caractères, lettres, 1 chiffre, 1 symbole',
                            'maxlength' => 255,
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmation du mot de passe',
                        'attr' => [
                            'maxlength' => 255,
                        ],
                    ],
                    'constraints' => [new PasswordRequirements()],
                ]
            )
        ;
    }
}
