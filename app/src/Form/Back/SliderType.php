<?php

namespace App\Form\Back;

use App\Entity\Product;
use App\Entity\Slider;
use App\Enum\ProductStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class SliderType extends AbstractType
{
    /**
     * <@inheritDoc>.
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
                    'query_builder' => static fn (EntityRepository $er) => $er->createQueryBuilder('p')
                        ->innerjoin('p.author', 'a')
                        ->addSelect('a')
                        ->where('p.status = :productStatus')
                        ->andWhere('a.isStripeAccountActive = true')
                        ->setParameter('productStatus', ProductStatus::VALIDATE->value)
                        // #
                        ->andWhere('p.deletedAt IS NULL')
                        ->andWhere('p.createdAt between :startDate and :endDate')
                        ->setParameter('startDate', (new DatePoint('-1 months'))->format('Y-m-d'))
                        ->setParameter('endDate', (new DatePoint())->format('Y-m-d'))
                        // #
                        ->orderBy('p.id, p.numberView', 'ASC')
                        ->addOrderBy('p.title', 'DESC')
                        ->setMaxResults(20),
                    'choice_label' => static fn (Product $product): string => $product->getTitle().' - Note : '.$product->getAverageNote().' - Nb avis : '.$product->getReviews()->count().' - Nb vue : '.$product->getNumberView(),
                    'placeholder' => '-- Sélectionner --',
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false,
                    'autocomplete' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Slider::class,
            ]
        );
    }
}
