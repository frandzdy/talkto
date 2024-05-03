<?php

namespace App\Form\Front;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => 'Expliquez pourquoi vous louer vos biens ou bien parlez de vous même tous simplement ...',
                        'style' => 'height: 200px',
                        'maxlength' => 255,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }

    public function getParent(): string
    {
        return UserType::class;
    }
}
