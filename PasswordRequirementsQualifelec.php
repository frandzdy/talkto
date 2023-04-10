<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation du mot de passe de l'application Qualifelec
 *
 * @Annotation
 */
class PasswordRequirementsQualifelec extends Constraint
{
    public $message = 'Mot de passe trop simple (minimum 8 caractères, avec au moins une majuscule, un chiffre et un symbole).';

    /**
     * Retourne le nom de la class pour valider le mot de passe
     */
    public function validatedBy()
    {
        return static::class . 'Validator';
    }
}
