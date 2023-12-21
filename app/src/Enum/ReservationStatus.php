<?php

namespace App\Enum;

/**
 * Status de la réservation.
 */
enum ReservationStatus: int
{
    case PENDING = 1;
    case IN_PROGRESS = 2;
    case FINISHED = 3;
    case CANCELED = 4;

    public static function getLabels(): array
    {
        return [
            ReservationStatus::PENDING->value => 'Réservation en attente',
            ReservationStatus::IN_PROGRESS->value => 'Réservation en cours',
            ReservationStatus::FINISHED->value => 'Réservation terminé',
            ReservationStatus::CANCELED->value => 'Réservation annulé',
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
