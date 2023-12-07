<?php

namespace App\Form\Front;


use App\Entity\Checkin;
use App\Enum\CheckinStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                'status',
                EnumType::class,
                [
                    'class' => CheckinStatus::class,
                    'choice_label' => 'label',
                    'label' => 'Statut du check',
                    'label_attr' => ['class' => 'form-text text-muted'],
                    'attr' =>
                        [
                            'data-action' => 'checkin#onChangeStatus'
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
                        ],
                    'required' => false
                ]
            )
            ->add('errorFile', HiddenType::class, ['mapped' => false])
            ->add('uploadedPictures', CollectionType::class, [
                'label' => false,
                'entry_type' => FileType::class,
                'entry_options' => [
                    'attr' => [
                        'accept' => 'image/*',
                        'lang' => 'fr',
                        'data-browse' => 'Votre photo'
                    ],
                ],
                'label_attr' => [
                    'class' => 'w-max-content form-text text-muted checkin-pictures',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ]);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit']);
    }

    /**
     * Contrôle les données avant de submit
     */
    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        /**
         * @var Checkin $checkin
         */
        $checkin = $event->getData();

        if (!$checkin) {
            return;
        }

        if ($checkin->getStatus() === CheckinStatus::VALIDATE_WITH_WARNING && !$checkin->getComments()) {
            $form->get('comments')->addError(new FormError('Information requise.'));
        }

        if (
            $checkin->getStatus() === CheckinStatus::VALIDATE_WITH_WARNING
            && array_key_exists(
                1,
                $checkin->uploadedPictures
            )
            && \is_null($checkin->uploadedPictures[1])
        ) {
            $form->get('errorFile')->addError(new FormError('Information requise.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Checkin::class,
            ]
        );
    }
}
