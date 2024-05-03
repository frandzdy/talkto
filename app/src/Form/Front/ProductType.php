<?php

namespace App\Form\Front;

use App\Entity\Product;
use App\Enum\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre du produit',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => 'table ...',
                        'maxlength' => 255,
                    ],
                ]
            )
            ->add(
                'shortDescription',
                TextareaType::class,
                [
                    'label' => 'Court description du produit',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => 'table en bois 8 places ...',
                        'style' => 'height: 200px',
                        'maxlength' => 255,
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => 'Détaillez les spécificités votre bien ...',
                        'rows' => 'height: 400px',
                        'data-controller' => 'editor',
                    ],
                    'purify_html' => true,
                ]
            )
            ->add('handleError', TextType::class)
            ->add(
                'uploadedPictures',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => FileType::class,
                    'entry_options' => [
                        'label' => false,
                        'attr' => [
                            'accept' => 'image/png, image/jpeg, image/jpg',
                            'lang' => 'fr',
                            'allow-delete' => true,
                        ],
                    ],
                    'label_attr' => [
                        'class' => 'w-max-content form-text text-muted',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            )
            ->add(
                'caution',
                TextType::class,
                [
                    'label' => 'Montant de la caution',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => '150 € ...',
                        'maxlength' => 11,
                    ],
                ]
            )
            ->add(
                'amount',
                TextType::class,
                [
                    'label' => 'Prix / jours',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'placeholder' => '10 € ...',
                        'maxlength' => 11,
                    ],
                ]
            )->add(
                'quantity',
                TextType::class,
                [
                    'label' => 'Quantité',
                    'label_attr' => [
                        'class' => 'form-text text-muted',
                    ],
                    'attr' => [
                        'maxlength' => 11,
                    ],
                ]
            )
            ->add(
                'category',
                EnumType::class,
                [
                    'class' => ProductCategory::class,
                    'choice_label' => 'label',
                    'label' => 'Catégorie du produit',
                ]
            )
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event): void {
            $product = $event->getData();
            $form = $event->getForm();
            if (!current($product->uploadedPictures) instanceof UploadedFile && !$product->getPictures()->count()) {
                $form->get('handleError')->addError(new FormError('Information requise.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['creation'],
        ]);
    }
}
