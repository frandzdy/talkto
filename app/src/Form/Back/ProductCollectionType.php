<?php

namespace App\Form\Back;

use App\Entity\HomePage;
use App\Entity\Product;
use App\Enum\ProductStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Sodium\add;

/**
 * Filtre du menu produit
 */
class ProductCollectionType extends AbstractType
{
    /**
     * <@inheritDoc>
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'product',
                EntityType::class,
                [
                    'label' => false,
                    'class' => Product::class,
                    'choice_label' => 'title',
                    'attr' =>
                        [
                            'placeholder' => '-- Sélectionner --',
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ],
                    'required' => true,
                    'autocomplete' => true,
                    'expanded' => false,
                    'multiple' => false
                ]
            )
        ;
    }
}
