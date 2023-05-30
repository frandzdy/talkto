<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation du mot de passe de l'application
 *
 * @Annotation
 */
class PasswordRequirements extends Constraint
{
    public string $message = 'Mot de passe trop simple (minimum 8 caractères, avec au moins une majuscule, un chiffre et un symbole).';

    /**
     * Retourne le nom de la class pour valider le mot de passe
     */
    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
