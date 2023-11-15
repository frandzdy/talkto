<?php

namespace App\Form\Front;


use App\Entity\Checkin;
use App\Enum\CheckinValidateStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckinType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'validateStatus',
                EnumType::class,
                [
                    'class' => CheckinValidateStatus::class,
                    'choice_label' => 'label',
                    'label' => 'Statut du check',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'data-action' => 'checkin#onChangeValidateStatus'
                        ]
                ]
            )
            ->add(
                'comments',
                TextareaType::class,
                [
                    'label' => 'Décrivez le problème',
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'style' => 'height: 200px;resize:none',
                            'placeholder' => 'Ajouter un commentaire détails',
                        ]
                ]
            )
            ->add('uploadedPictures', CollectionType::class, [
                'label' => 'Pièces jointes < 5Mo',
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => 'votre photo',
                    'attr' => [
                        'accept' => 'image/*',
                        'lang' => 'fr',
                        'data-browse' => 'votre photo'
                    ],
                ],
                'label_attr' => [
                    'class' => 'w-max-content form-text text-muted checkin-pictures',
                    'style' => 'display: none'
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Checkin::class,
            ]
        );
    }
}
