<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ProductReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'jj/mm/aaaa',
                            'maxlength' => 11,
                            'data-controller' => 'datetimepicker',
                            'class' => 'text-center'
                        ],
                    'constraints' =>
                        [
                            new NotBlank(['message' => 'Information requise.'])
                        ],
                    'required' => true
                ]
            )->add('quantity', ChoiceType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => '4',
                            'maxlength' => 11,
                            'max' => $options['quantityLeft']
                        ],
                    'constraints' =>
                        [
                            new Range(['min' => 0, 'max' => $options['quantityLeft'], 'notInRangeMessage' => ''])
                        ],
                    'choices' => [
                        0 => 0,
                        1 => 1,
                    ],
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'quantityLeft' => null
        ]);
    }
}
