<?php

namespace App\Form\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'profile__edit-input',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                    new Email(['message' => 'Format E-mail incorrect.']),
                    new NoSuspiciousCharacters([
                        'restrictionLevelMessage' => 'Information erronée.',
                        'invisibleMessage' => 'Information erronée.',
                        'mixedNumbersMessage' => 'Information erronée.',
                        'hiddenOverlayMessage' => 'Information erronée.',
                        'restrictionLevel' => NoSuspiciousCharacters::RESTRICTION_LEVEL_HIGH,
                    ]),
                ],
            ])
        ;
    }
}
