<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Contrainte de validation du mot de passe de l'application.
 */
#[\Attribute]
class PasswordRequirements extends Constraint
{
    public string $message = 'Mot de passe trop simple (minimum 10 caractères, avec au moins une majuscule, un chiffre et un symbole).';

    public string $mode = 'strict';

    // all configurable options must be passed to the constructor
    public function __construct(?string $mode = null, ?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->mode = $mode ?? $this->mode;
        $this->message = $message ?? $this->message;
    }

    /**
     * Retourne le nom de la class pour valider le mot de passe.
     */
    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
