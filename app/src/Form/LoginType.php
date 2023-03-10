<?php
    
    namespace App\Form;
    
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Constraints\NotBlank;

    class LoginType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->setMethod("POST")
                ->add(
                    'email',
                    EmailType::class,
                    [
                        'label' => 'Adresse e-mail',
                        'label_attr' =>
                            [
                                'class' => 'form-label'
                            ],
                        'attr' =>
                            [
                                'placeholder' => 'Adresse e-mail',
                                'maxlength' => 255
                            ],
                        'constraints' =>
                            [
                                new NotBlank(['message' => 'Information requises.']),
                                new Email(['message' => 'Information requises.'])
                            ]
                    ]
                );
        }
    }
