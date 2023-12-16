<?php

namespace App\Form\Back;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->innerjoin('p.author', 'a')
                            ->innerjoin('p.reviews', 'r')
                            ->addSelect('a')
                            ->where('p.status = :productStatus')
                            ->andWhere('a.isStripeAccountActive = true')
                            ->setParameter('productStatus', ProductStatus::VALIDATE->value)
                            ##
                            ->andWhere('p.createdAt between :startDate and :endDate')
                            ->setParameter('startDate', (new DatePoint('-1 months'))->format('Y-m-d'))
                            ->setParameter('endDate', (new DatePoint())->format('Y-m-d'))
                            ->having('(avg(r.note) BETWEEN 3 AND 5 OR avg(r.note) BETWEEN 0 AND 3)')
                            ->andWhere('(p.numberView >= 50 or p.numberView <= 50)')
                            ##
                            ->orderBy('p.numberView, p.title', 'ASC')
                            ->groupBy('p.id')
                            ;
                    },
                    'choice_label' => function (Product $product) {
                        return $product->getTitle() . ' - Note : ' . $product->getAverageNote() . ' ' . $product->getAverageNote();
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
