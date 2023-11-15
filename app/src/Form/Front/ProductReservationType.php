<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                    'required' => true
                ]
            )->add('quantity', ChoiceType::class,
                [
                    'label' => false,
                    'placeholder' => '-- Sélectionnez une quantité --',
                    'attr' =>
                        [
                            'class' => 'text-center'
                        ],
                    'choices' => array_flip($options['choicesValue']),
                    'required' => true
                ]
            );
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit']);
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        if (!$form->get('quantity')->getData()) {
            $form->get('quantity')->addError(new FormError('Information requise.'));
        }
        if (!$form->get('date')->getData()) {
            $form->get('date')->addError(new FormError('Information requise.'));
        }
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
