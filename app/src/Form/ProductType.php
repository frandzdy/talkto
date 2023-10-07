<?php

namespace App\Form;

use App\Entity\Product;
use App\Enum\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => 'Titre du produit',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'maxlength' => 255
                        ]
                ])
            ->add('description', TextareaType::class,
                [
                    'label' => 'Description',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Détaillez votre bien ...',
                            'style' => 'height: 200px',
                            'maxlength' => 255
                        ]
                ]
            )
            ->add('uploadedPictures', CollectionType::class, [
                'label' => false,
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'accept' => 'image/*',
                        'allow-delete' => true,
                        'data-browse' => 'votre photo'
                    ],
                ],
                'label_attr' => [
                    'class' => 'w-max-content form-text text-muted',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('caution', TextType::class,
                [
                    'label' => 'Montant de la caution',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => '150',
                            'maxlength' => 11,
                        ]
                ]
            )
            ->add('amount', TextType::class,
                [
                    'label' => 'Prix / Mois',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => '150',
                            'maxlength' => 11,
                        ]
                ]
            )->add('quantity', TextType::class,
                [
                    'label' => 'Quantité',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => '4',
                            'maxlength' => 11,
                        ]
                ]
            )
            ->add(
                'category',
                EnumType::class,
                [
                    'class' => ProductCategory::class,
                    'choice_label' => 'label',
                    'label' => 'Catégorie du produit',
                    'label_attr' => ['class' => 'form-text text-muted']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
