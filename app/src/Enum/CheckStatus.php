<?php

namespace App\Enum;

/**
 * Status du check
 */
enum CheckStatus: int
{
    case IN = 1;
    case OUT = 2;
    public static function getLabels(): array
    {
        return [
            CheckStatus::IN->value => 'Check-in',
            CheckStatus::OUT->value => 'Check-out',
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
