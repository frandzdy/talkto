<?php

namespace App\Enum;

/**
 * Status de la ligne de transaction
 */
enum TransactionLineStatus: int
{
    case WAITING = 1;
    case VALIDATE = 2;
    case FINISHED = 3;
    case CANCELED = 4;

    public static function getLabels(): array
    {
        return [
            TransactionLineStatus::WAITING->value => 'En attente de réservation',
            TransactionLineStatus::VALIDATE->value => 'Réservation en cours',
            TransactionLineStatus::FINISHED->value => 'Réservation terminé',
            TransactionLineStatus::CANCELED->value => 'Réservation annulé',
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
