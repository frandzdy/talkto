<?php

namespace App\Form\Front;

use App\Entity\Product;
use App\Entity\TransactionLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReservationType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date', TextType::class,
                [
                    'label' => false,
                    'attr' =>
                        [
                            'placeholder' => 'Définissez votre date',
                            'maxlength' => 11,
                            'data-controller' => 'datetimepicker',
                            'data-disabled-dates' => json_encode($options['disabledDates']),
                            'data-token' => $options['token'],
                            'class' => 'text-center'
                        ],
                    'required' => true
                ]
            )->add(
                'quantity', ChoiceType::class,
                [
                    'label' => false,
                    'placeholder' => '-- Sélectionnez une quantité --',
                    'attr' =>
                        [
                            'class' => 'text-center'
                        ],
                    'choices' => array_flip($options['choicesValue']),
                    'required' => true
                ]
            );
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();
            if (!$form->get('quantity')->getData()) {
                $form->get('quantity')->addError(new FormError('Information requise.'));
            }
            if (!$form->get('date')->getData()) {
                $form->get('date')->addError(new FormError('Information requise.'));
            }
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $array = $event->getData();
            $form = $event->getForm();
            $options = $event->getForm()->getConfig()->getOptions();
            $date = explode('au', $array['date']);

            $product = $this->em->getRepository(Product::class)->findOneBy(['token' => $options['token']]);
            $transactionLines = $this->em->getRepository(TransactionLine::class)->productCheckQuantityAvailable(
                $product,
                new \DateTime($date[0])
            );
            $totalReserved = 0;
            foreach ($transactionLines as $transactionLine) {
                $totalReserved = $transactionLine->getQuantity();
            }

            $choicesValue = [];
            if ($quantityLeft = $product->getQuantity() - $totalReserved) {
                for ($i = 1; $i <= $quantityLeft; $i++) {
                    $choicesValue[$i] = $i;
                }
            }

            $form->remove('quantity')
                ->add(
                    'quantity', ChoiceType::class,
                    [
                        'label' => false,
                        'placeholder' => '-- Sélectionnez une quantité --',
                        'attr' =>
                            [
                                'class' => 'text-center'
                            ],
                        'choices' => array_flip($choicesValue),
                        'required' => true
                    ]
                );
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'quantityLeft' => null,
            'choicesValue' => null,
            'disabledDates' => null,
            'token' => null,
        ]);
    }
}
