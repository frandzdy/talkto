<?php

namespace App\Form\Front;

use App\Enum\ContactSubject;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'maxlength' => 100,
                        'placeholder' => 'Prénom *',
                    ],
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'maxlength' => 100,
                        'placeholder' => 'Nom *',
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'maxlength' => 255,
                        'placeholder' => 'E-mail *',
                    ],
                ]
            )
            ->add(
                'subject',
                ChoiceType::class,
                [
                    'label' => false,
                    'attr' => ['class' => 'custom-select'],
                    'choices' => array_flip(ContactSubject::getAvailableContactSubjects()),
                    'placeholder' => '-- Sélectionnez un sujet --',
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'label' => false,
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'attr' => [
                        'style' => 'height: 200px;resize:none;',
                        'maxlength' => 400,
                        'placeholder' => 'Laissez nous un message *',
                    ],
                ]
            )
        ;
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
