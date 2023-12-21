<?php

namespace App\Enum;

/**
 * ContactSubject.
 */
enum ContactSubject: int
{
    public const SUBJECT_BUG = 1;

    public const SUBJECT_IMPROVEMENT = 2;

    public const SUBJECT_LESSOR = 3;

    public const SUBJECT_CAUTION = 4;

    public const SUBJECT_PERSONAL_DATA = 5;

    /**
     * Retourne la liste des sujets contacts disponibles.
     */
    public static function getAvailableContactSubjects(): array
    {
        return [
            self::SUBJECT_BUG => 'Signaler une anomalie ?',
            self::SUBJECT_IMPROVEMENT => "Suggestion d'amélioration",
            self::SUBJECT_LESSOR => 'Gestion de votre compte bailleur',
            self::SUBJECT_CAUTION => 'Une question sur la caution ?',
            self::SUBJECT_PERSONAL_DATA => 'Gestion de vos données personnelles',
        ];
    }

    /**
     * Retourne la liste des sujets contacts disponibles.
     */
    public static function getLabel($key): string
    {
        $arrayContactSubject = self::getAvailableContactSubjects();

        return $arrayContactSubject[$key];
    }

    /**
     * Affiche le label de l'item.
     */
    public function label(): ?string
    {
        return self::getAvailableContactSubjects()[$this->value] ?? null;
    }
}
