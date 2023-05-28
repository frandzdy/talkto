<?php

namespace App\Entity;

/**
 * Children
 */
final class Children
{
    public const WANT_KIDS = 1;
    public const DONT_WANT_KIDS = 2;

    /**
     * Retourne la liste des types d'enfants disponibles
     */
    public static function getAvailableChildrens(): array
    {
        return [
            self::WANT_KIDS => "Je veux des enfants",
            self::DONT_WANT_KIDS => "Non merci"
        ];
    }
}
