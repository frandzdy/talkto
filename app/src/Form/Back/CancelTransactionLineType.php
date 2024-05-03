<?php

namespace App\Form\Back;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulaire d'annulation d'une transaction.
 */
class CancelTransactionLineType extends AbstractType
{
    /**
     * <@inheritDoc>.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'amount',
                TextType::class,
                [
                    'label' => 'Montant à rembourser :',
                    'required' => true,
                    'constraints' => [
                        new NotBlank(message: 'Information requis'),
                        new LessThan(value: $options['maxAmount'], message: 'Le montant ne peut être supérieur au montant de la transaction initial.'),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['maxAmount' => null])
        ;
    }
}
