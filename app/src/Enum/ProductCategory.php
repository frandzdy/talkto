<?php

namespace App\Enum;

/**
 * Catégorie de produit.
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

    public static function getUriLabels(): array
    {
        return [
            ProductCategory::TOOLS->value => [
                'title' => 'Petit / Moyen outillages',
                'slug' => 'petit-moyen-outillages',
            ],
            ProductCategory::GOODS->value => [
                'title' => 'Biens',
                'slug' => 'biens',
            ],
            ProductCategory::FURNITURE->value => [
                'title' => 'Mobiliers',
                'slug' => 'mobiliers',
            ],
            ProductCategory::OTHERS->value => [
                'title' => 'Autres',
                'slug' => 'autres',
            ],
        ];
    }

    /**
     * Affiche le label de l'item.
     */
    public function label(): ?string
    {
        return self::getLabels()[$this->value] ?? null;
    }

    public function labelUri(): ?array
    {
        return self::getUriLabels()[$this->value] ?? null;
    }
}
