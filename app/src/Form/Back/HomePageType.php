<?php

namespace App\Form\Back;

use App\Entity\HomePage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class HomePageType extends AbstractType
{
    /**
     * <@inheritDoc>.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre', 'required' => true])
            ->add(
                'websiteContents',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => WebsiteContentType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => true,
                    'by_reference' => false,
                ]
            )
            ->add(
                'sliders',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => SliderType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => true,
                    'by_reference' => false,
                ]
            )
            ->add(
                'underSliders',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => UnderSliderType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => true,
                    'by_reference' => false,
                ]
            )
            ->add(
                'mids',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => MidType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => true,
                    'by_reference' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('data_class', HomePage::class)
        ;
    }
}
