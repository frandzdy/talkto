<?php

namespace App\Form\Back;

use App\Entity\HomePage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit
 */
class HomePageType extends AbstractType
{
    /**
     * <@inheritDoc>
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'sliders',
                CollectionType::class,
                [
                    'entry_type' => ProductCollectionType::class,
                    'label' => false,
                    'required' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Titre, description ou propriétaire',
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ],
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
            ->add(
                'slides',
                CollectionType::class,
                [
                    'entry_type' => ProductCollectionType::class,
                    'label' => false,
                    'required' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Titre, description ou propriétaire',
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'mapped' => false
                ]
            )
            ->add(
                'trends',
                CollectionType::class,
                [
                    'entry_type' => ProductCollectionType::class,
                    'label' => false,
                    'required' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Titre, description ou propriétaire',
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'mapped' => false
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
            ->setDefault('data_class', HomePage::class)
            ;
    }
}
