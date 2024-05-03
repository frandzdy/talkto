<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse e-mail',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'placeholder' => 'Adresse e-mail',
                        'maxlength' => 255,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Information requises.']),
                        new Email(['message' => 'Information requises.']),
                    ],
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'placeholder' => 'Adresse e-mail',
                        'maxlength' => 255,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Information requises.']),
                        new Email(['message' => 'Information requises.']),
                    ],
                    'toggle' => true,
                    'translation_domain' => 'messages',
                    'hidden_label' => 'Masquer',
                    'visible_label' => 'Afficher',
                ]
            )
        ;
    }
}
