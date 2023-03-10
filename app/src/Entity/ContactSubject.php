<?php

namespace App\Entity;

/**
 * ContactSubject
 */
final class ContactSubject
{
    public const SUBJECT_MATCH = 1;
    public const SUBJECT_PRIVATE_CHAT = 2;
    public const SUBJECT_ACCOUNT = 3;
    public const SUBJECT_ABONNEMENT = 4;
    public const SUBJECT_PERSONAL_DATA = 5;

    /**
     * Retourne la liste des sujets contacts disponibles
     */
    public static function getAvailableContactSubjects(): array
    {
        return [
            self::SUBJECT_MATCH => "Match",
            self::SUBJECT_PRIVATE_CHAT => "Chat privé",
            self::SUBJECT_ACCOUNT => "Accès gestion de votre compte",
            self::SUBJECT_ABONNEMENT => "Gestion de votre abonnement",
            self::SUBJECT_PERSONAL_DATA => "Gestion de vos données personnelles",
        ];
    }
    
    /**
     * Retourne la liste des sujets contacts disponibles
     */
    public static function getLabel($key): string
    {
        $arrayContactSubject = self::getAvailableContactSubjects();
        
        return $arrayContactSubject[$key];
    }
    
}
