<?php

namespace App\Form\Back;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class ProductType extends AbstractType
{
    /**
     * <@inheritDoc>.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'status',
                EnumType::class,
                [
                    'class' => ProductStatus::class,
                    'choice_label' => 'label',
                    'label' => 'Statut du produit :',
                    'placeholder' => '-- Sélectionner le statut --',
                ]
            )
            ->add(
                'responseRejected',
                TextareaType::class,
                [
                    'label' => 'Raison du refus :',
                    'attr' => [
                        'placeholder' => 'Si le produit est rejeté alors expliquez pourquoi ...',
                        'style' => 'height: 200px',
                        'maxlength' => 255,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('data_class', Product::class)
        ;
    }
}
