<?php

namespace App\Enum;

/**
 * Status de la transaction
 */
enum TransactionStatus: int
{
    case WAITING = 1;
    case VALIDATE = 2;
    case REJECTED = 3;

    public static function getLabels(): array
    {
        return [
            TransactionStatus::WAITING->value => 'En attente de paiement',
            TransactionStatus::VALIDATE->value => 'Paiement validé',
            TransactionStatus::REJECTED->value => 'Paiement rejeté',
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
