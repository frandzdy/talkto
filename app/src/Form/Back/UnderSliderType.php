<?php

namespace App\Form\Back;

use App\Entity\UnderSlider;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Filtre du menu produit.
 */
class UnderSliderType extends SliderType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => UnderSlider::class,
            ]
        );
    }
}
