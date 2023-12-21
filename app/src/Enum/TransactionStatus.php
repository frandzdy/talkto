<?php

namespace App\Enum;

/**
 * Status de la transaction.
 */
enum TransactionStatus: int
{
    case WAITING = 1;
    case VALIDATE = 2;
    case CANCELED = 3;

    public static function getLabels(): array
    {
        return [
            TransactionStatus::WAITING->value => 'En attente de paiement',
            TransactionStatus::VALIDATE->value => 'Paiement validé',
            TransactionStatus::CANCELED->value => 'Paiement annulé',
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
