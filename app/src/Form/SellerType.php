<?php

namespace App\Form;

use App\Entity\Children;
use App\Entity\Genre;
use App\Entity\InterestExit;
use App\Entity\InterestHobbies;
use App\Entity\InterestSports;
use App\Entity\Smoker;
use App\Entity\User;
use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use \Symfony\Component\Form\Extension\Core\Type\EmailType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SellerType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'label_attr' => ['class'=>'form-text text-muted'],
                    'attr' =>
                        [
                            'placeholder' => 'Expliquez pourquoi vous louer vos biens ou bien parlez de vous même tous simplement ...',
                            'style' => 'height: 200px',
                            'maxlength' => 255
                        ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class
            ]
        );
    }

    public function getParent(): string
    {
        return UserType::class;
    }
}
