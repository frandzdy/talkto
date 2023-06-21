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
                            'placeholder' => 'Définissez votre date',
                            'maxlength' => 11,
                            'data-controller' => 'datetimepicker',
                            'data-disabled-dates' => json_encode($options['disabledDates']),
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
                    'placeholder' => '- Sélectionnez une quantité -',
                    'attr' =>
                        [
                            'class' => 'text-center'
                        ],
                    'constraints' =>
                        [
                            new Range(['min' => 1, 'max' => $options['quantityLeft'], 'notInRangeMessage' => ''])
                        ],
                    'choices' => $options['choicesValue'],
                    'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'quantityLeft' => null,
            'choicesValue' => null,
            'disabledDates' => null,
        ]);
    }
}
