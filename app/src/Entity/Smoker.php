<?php

namespace App\Entity;

/**
 * Smoker
 */
final class Smoker
{
    public const NO_SMOKER = 1;
    public const SMOKER = 2;
    public const SMOKER_OR_NOT = 3;

    /**
     * Retourne la liste des types de fumeur disponibles
     */
    public static function getAvailableSmokers(): array
    {
        return [
            self::NO_SMOKER => "Non-fumeur",
            self::SMOKER => "Fumeur",
            self::SMOKER_OR_NOT => "Ca me dérange pas"
        ];
    }
}
