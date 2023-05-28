<?php

namespace App\Entity;

/**
 * Children
 */
final class Abonnement
{
    /**
     * Retourne la liste des différents abonnements
     */
    public static function getAvailableAbonnements(string $type): array
    {
        $advantage = [
            'Spamis' => 'Illimité profitez au maximum.',
            'Localisation' => 'Géolocalisation plus précise.',
            'Match' => 'Visualisation des matchs reçus.',
            'Paramètre' => 'Recherche avancée.'
        ];
        
        $abo = [
            'abo1' => [
                'name' => 'Abonnement Spami',
                'price' => '14.99',
                'advangate' => $advantage
                
            ],
            'abo2' => [
                'name' => 'Abonnement Spammeur',
                'price' => '59.99',
                'advangate' => $advantage
            ],
            'abo3' => [
                'name' => 'Abonnement Spam Lover',
                'price' => '99.99',
                'advangate' => $advantage
            ]
        ];
        
        return $abo[$type];
    }
}
