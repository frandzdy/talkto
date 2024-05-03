<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\RentedExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RentedExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [RentedExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getProductCategories', [RentedExtensionRuntime::class, 'getProductCategories'], ['is_safe' => ['html']]),
            new TwigFunction('numberClaims', [RentedExtensionRuntime::class, 'numberClaims']),
            new TwigFunction('numberProductToValidate', [RentedExtensionRuntime::class, 'numberProductToValidate']),
            new TwigFunction('getInvoiceLink', [RentedExtensionRuntime::class, 'getInvoiceLink']),
            new TwigFunction('getNumberReservationOnProgress', [RentedExtensionRuntime::class, 'getNumberReservationOnProgress']),
            new TwigFunction('getAccountLink', [RentedExtensionRuntime::class, 'getAccountLink']),
            new TwigFunction('getDistance', [RentedExtensionRuntime::class, 'getDistance']),
        ];
    }
}
