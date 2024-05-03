<?php

namespace App\Form\Back;

use App\Entity\Mid;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class MidType extends SliderType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Mid::class,
            ]
        );
    }
}
