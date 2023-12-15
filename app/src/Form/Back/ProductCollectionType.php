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
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->innerjoin('p.author', 'a')
                            ->addSelect('a')
                            ->where('p.status = :productStatus')
                            ->andWhere('a.isStripeAccountActive = true')
                            ->setParameter(':productStatus', ProductStatus::VALIDATE)
                            ->orderBy('p.title')
                            ;
                    },
                    'attr' =>
                        [
                            'style' => 'width: 56%;',
                            'class' => 'float-right'
                        ],
                    'placeholder' => '-- Sélectionner --',
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false
                ]
            )
        ;
    }
}
