<?php
    
    namespace App\Form;
    
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\Length;
    use Symfony\Component\Validator\Constraints\NotBlank;

    class SignalType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->setMethod("POST")
                ->add(
                    'nom',
                    TextType::class,
                    [
                        'label' => 'Nom',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'placeholder' => 'Nom',
                                'maxlength' => 255
                            ],
                        'constraints' =>
                            [
                                new NotBlank(['message' => 'Information requises.']),
                            ]
                    ]
                )
                ->add(
                    'prenom',
                    TextType::class,
                    [
                        'label' => 'Prénom',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'placeholder' => 'Prénom',
                                'maxlength' => 255
                            ],
                        'constraints' =>
                            [
                                new NotBlank(['message' => 'Information requises.']),
                            ]
                    ]
                )->add(
                    'email',
                    EmailType::class,
                    [
                        'label' => 'E-mail',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'placeholder' => 'E-mail',
                                'maxlength' => 255
                            ],
                        'constraints' =>
                            [
                                new NotBlank(['message' => 'Information requises.']),
                                new Email(['message' => 'Information requises.']),
                            ]
                    ]
                )->add(
                    'description',
                    TextareaType::class,
                    [
                        'label' => 'Si vous souhaitez détailler le problème rencontrer',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'placeholder' => 'Si vous souhaitez détailler le problème rencontrer',
                                'style' => 'height: 200px',
                                'maxlength' => 255
                            ],
                        'required' => false
                    ]
                );
        }
    }
