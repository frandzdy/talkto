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

class UserPaymentType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('picture')
            ->remove('plainPassword');
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
