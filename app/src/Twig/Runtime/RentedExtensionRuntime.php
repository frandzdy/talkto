<?php

namespace App\Twig\Runtime;

use App\Enum\ProductCategory;
use Twig\Extension\RuntimeExtensionInterface;

class RentedExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getProductCategories()
    {
        return ProductCategory::getLabels();
    }
}
