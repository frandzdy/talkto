<?php
    
    namespace App\Form;
    
    use App\Entity\ContactSubject;
    use App\Entity\WebsiteContactSubject;
    use App\Model\ContactModel;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ContactType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);
            
            $builder
                ->add(
                    'firstname',
                    TextType::class,
                    [
                        'label' => 'Prénom *',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'maxlength' => 100,
                                'placeholder' => 'Prénom *'
                            ]
                    ]
                )
                ->add(
                    'lastname',
                    TextType::class,
                    [
                        'label' => 'Nom *',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'maxlength' => 100,
                                'placeholder' => 'Nom *'
                            ]
                    ]
                )
                ->add(
                    'email',
                    EmailType::class,
                    [
                        'label' => 'E-mail *',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'maxlength' => 255,
                                'placeholder' => 'E-mail *'
                            ]
                    ]
                )
                ->add(
                    'subject',
                    ChoiceType::class,
                    [
                        'label' => 'Sujet de votre demande',
                        'attr' => ['class' => 'custom-select'],
                        'choices' => array_flip(ContactSubject::getAvailableContactSubjects()),
                        'placeholder' => 'Sélectionnez un sujet'
                    ]
                )
                ->add(
                    'message',
                    TextareaType::class,
                    [
                        'label' => 'Laissez nous un message *',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'style' => 'height: 200px',
                                'maxlength' => 255,
                                'placeholder' => 'Laissez nous un message *',
                            ]
                    ]
                );
        }
    
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults(
                [
                    'data_class' => ContactModel::class,
                ]
            );
        }
    }
