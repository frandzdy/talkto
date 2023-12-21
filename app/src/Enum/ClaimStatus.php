<?php

namespace App\Enum;

/**
 * Status de la ligne de transaction.
 */
enum ClaimStatus: int
{
    case PENDING = 1;
    case FINISHED = 2;

    public static function getLabels(): array
    {
        return [
            ClaimStatus::PENDING->value => "En cours d'analyse",
            ClaimStatus::FINISHED->value => 'Terminer',
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
