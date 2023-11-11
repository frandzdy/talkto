<?php

namespace App\Form\Type\Back;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu client
 */
class CustomerAccountFilterType extends AbstractType
{
    /**
     * <@inheritDoc>
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add(
                'term',
                TextType::class,
                [
                    'label' => false,
                    'required' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Siret/N° identification, n° client, raison sociale, nom ou email utilisateur',
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ]
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('csrf_protection', false);
    }
}
