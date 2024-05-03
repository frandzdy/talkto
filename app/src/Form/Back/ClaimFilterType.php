<?php

namespace App\Form\Back;

use App\Enum\ClaimStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class ClaimFilterType extends AbstractType
{
    /**
     * <@inheritDoc>.
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
                    'attr' => [
                        'placeholder' => 'Titre, description ou propriétaire',
                        'style' => 'width: 53%;',
                        'class' => 'float-right',
                    ],
                ]
            )
            ->add(
                'status',
                EnumType::class,
                [
                    'class' => ClaimStatus::class,
                    'label' => false,
                    'choice_label' => 'label',
                    'attr' => [
                        'style' => 'width: 38%;',
                        'class' => 'float-right',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('csrf_protection', false)
        ;
    }
}
