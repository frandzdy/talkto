<?php

namespace App\Form;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class)
            ->add('uploadedPictures', CollectionType::class, [
                'label' => 'PJ (PDF < 5Mo)',
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'accept' => 'image/*',
                        'allow-delete' => true,
                    ],
                ],
                'label_attr' => [
                    'class' => 'w-max-content',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
            ->add('amount');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
