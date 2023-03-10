<?php

namespace App\Entity;

/**
 * InterestSports
 */
final class InterestSports
{
    public const SWIMMING = 1;
    public const BIKE = 2;
    public const TREK = 3;
    public const FISHING = 4;
    public const SKI = 5;
    public const MMA = 6;
    public const SPORT_DRIVE = 7;
    public const SPORT_COLLECTIF = 8;
    public const FOOTING = 9;
    public const TENNIS = 10;
    public const FITNESS = 11;
    public const SPORT_EXTREME = 12;

    /**
     * Retourne la liste des intérêts sportifs disponibles
     */
    public static function getAvailableInterestSports(): array
    {
        return [
            self::SWIMMING => "Natation",
            self::BIKE => "Vélo / VTT / Cross",
            self::TREK => "Randonnées",
            self::FISHING => "Pêcher",
            self::SKI => "Skier",
            self::MMA => "Sports de combat",
            self::SPORT_DRIVE => "Sports automobiles",
            self::SPORT_COLLECTIF => "Sports collectifs",
            self::FOOTING => "Footing",
            self::TENNIS => "Tennis",
            self::FITNESS => "Fitness / Musculation",
            self::SPORT_EXTREME => "Sports extrêmes"
        ];
    }
}
