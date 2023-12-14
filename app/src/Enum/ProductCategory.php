<?php

namespace App\Enum;

/**
 * Catégorie de produit
 */
enum ProductCategory: int
{
    case TOOLS = 0;
    case GOODS = 1;
    case FURNITURE = 2;
    case OTHERS = 3;

    public static function getLabels(): array
    {
        return [
            ProductCategory::TOOLS->value => 'Petit / Moyen outillages',
            ProductCategory::GOODS->value => 'Biens',
            ProductCategory::FURNITURE->value => 'Mobiliers',
            ProductCategory::OTHERS->value => 'Autres',
        ];
    }

    /**
     * Affiche le label de l'item
     */
    public function label(): ?string
    {
        return self::getLabels()[$this->value] ?? null;
    }
}
