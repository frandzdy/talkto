<?php
    
    namespace App\Form;
    
    
    use App\Entity\Checkin;
    use App\Entity\WebsiteContactSubject;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class CheckinType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);
            
            $builder
                ->add('comments', TextareaType::class, [
                    'label' => false,
                    'label_attr' =>
                        [
                            'class' => 'form-label'
                        ],
                    'attr' =>
                        [
                            'style' => 'height: 200px;resize:none;',
                            'maxlength' => 255,
                            'placeholder' => 'Ajouter un commentaire pour votre état du produit. Pourra être analyser en cas de contestation.',
                        ]
                ])
                ->add('uploadedPictures', CollectionType::class, [
                    'label' => false,
                    'entry_type' => FileType::class,
                    'entry_options' => [
                        'label' => false,
                        'attr' => [
                            'accept' => 'image/*',
                            'allow-delete' => true,
                        ],
                    ],
                    'label_attr' => [
                        'class' => 'w-max-content form-text text-muted',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                ])
                ;
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
