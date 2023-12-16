<?php

namespace App\Form\Back;

use App\Enum\ClaimStatus;
use App\Enum\ProductStatus;
use App\Enum\TransactionStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit
 */
class TransactionFilterType extends AbstractType
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
                            'placeholder' => 'Titre, description ou propriétaire',
                            'style' => 'width: 53%;',
                            'class' => 'float-right'
                        ]
                ]
            );
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
