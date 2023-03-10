<?php

namespace App\Entity;

/**
 * InterestHobbies
 */
final class InterestHobbies
{
    public const MOVIES = 1;
    public const READING = 2;
    public const DO_IT_YOURSELF = 3;
    public const GARDENING = 4;
    public const PICTURES = 5;
    public const DRAWING = 6;
    public const DANCE = 7;

    /**
     * Retourne la liste des genres disponibles
     */
    public static function getAvailableInterestHobbies(): array
    {
        return [
            self::MOVIES => "Cinéphile",
            self::READING => "Lire",
            self::DO_IT_YOURSELF => "Bricoler",
            self::GARDENING => "Jardiner",
            self::PICTURES => "Photographier",
            self::DRAWING => "Dessiner / Peindre",
            self::DANCE => "Danser",
        ];
    }
}
