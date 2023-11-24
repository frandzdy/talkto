<?php

namespace App\Form\Back;

use App\Entity\HomePage;
use App\Entity\Picture;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
                'sliders1',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'sliders2',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'sliders3',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'underSliders1',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'underSliders2',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'underSliders3',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'mids1',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )->add(
                'mids2',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Sélectionner --',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => false,
                    'required' => false,
                ]
            )
//            ->add(
//                'mids3',
//                EntityType::class,
//                [
//                    'class' => Product::class,
//                    'choice_label' => 'title',
//                    'placeholder' => '-- Sélectionner --',
//                    'expanded' => false,
//                    'multiple' => false,
//                    'label' => false,
//                    'required' => false,
//                ]
//            )
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
