<?php

namespace App\Enum;

/**
 * Status de la réservation
 */
enum ReservationStatus: int
{
    case WAITING = 1;
    case VALIDATE = 2;
    case FINISHED = 3;
    case CANCELED = 4;

    public static function getLabels(): array
    {
        return [
            ReservationStatus::WAITING->value => 'En attente de réservation',
            ReservationStatus::VALIDATE->value => 'Réservation en cours',
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
