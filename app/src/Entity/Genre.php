<?php

namespace App\Entity;

/**
 * Genre
 */
final class Genre
{
    public const CIVILITY_MEN = 1;
    public const CIVILITY_WOMEN = 2;
    public const CIVILITY_IEL = 3;

    /**
     * Retourne la liste des genres disponibles
     */
    public static function getAvailableGenres(): array
    {
        return [
            self::CIVILITY_MEN => "Il",
            self::CIVILITY_WOMEN => "Elle",
            self::CIVILITY_IEL => "Iel"
        ];
    }
}
