<?php

namespace App\Enum;

/**
 * Status de la réservation
 */
enum ReservationStatus: int
{
    case PENDING = 1;
    case FINISHED = 2;
    case CANCELED = 3;

    public static function getLabels(): array
    {
        return [
            ReservationStatus::PENDING->value => 'Réservation en cours',
            ReservationStatus::FINISHED->value => 'Réservation terminé',
            ReservationStatus::CANCELED->value => 'Réservation annulé',
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
