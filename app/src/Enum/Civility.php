<?php

namespace App\Enum;

/**
 * Civilités.
 */
enum Civility: int
{
    case MEN = 1;
    case WOMEN = 2;

    public static function getLabels(): array
    {
        return [
            Civility::MEN->value => 'M',
            Civility::WOMEN->value => 'Mme',
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
