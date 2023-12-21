<?php

namespace App\Enum;

/**
 * Status du produit.
 */
enum ProductStatus: int
{
    case WAITING = 1;
    case VALIDATE = 2;
    case REJECTED = 3;

    public static function getLabels(): array
    {
        return [
            ProductStatus::WAITING->value => 'En attente de validation',
            ProductStatus::VALIDATE->value => 'Validé',
            ProductStatus::REJECTED->value => 'Rejeté',
        ];
    }

    /**
     * Affiche le label de l'item.
     */
    public function label(): ?string
    {
        return self::getLabels()[$this->value] ?? null;
    }
}
