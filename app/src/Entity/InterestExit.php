<?php

namespace App\Entity;

/**
 * InterestExit
 */
final class InterestExit
{
    public const CINEMA = 1;
    public const LOVING_WE = 2;
    public const RESTAURANT = 3;
    public const JOURNEY = 4;
    public const PARTY = 5;
    public const SHOPPING = 6;
    public const CONCERT = 7;
    public const SHOW = 8;
    public const CULTURAL_JOURNEY = 9;

    /**
     * Retourne la liste des intérêts sortis disponibles
     */
    public static function getAvailableInterestExits(): array
    {
        return [
            self::CINEMA => "Cinéma",
            self::LOVING_WE => "Week-ends en amoureux",
            self::JOURNEY => "Balades (mer, forêt, plage, etc)",
            self::RESTAURANT => "Restaurants",
            self::PARTY => "Fêtes \ soirée",
            self::SHOPPING => "Shopping",
            self::CONCERT => "Concerts",
            self::SHOW => "Spectacles",
            self::CULTURAL_JOURNEY => "Sortie culturelle"
        ];
    }
}
