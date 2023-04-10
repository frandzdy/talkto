<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validateur de mot de passe pour l'application Qualifelec
 *
 * @Annotation
 */
class PasswordRequirementsQualifelecValidator extends ConstraintValidator
{
    /**
     * Valide les données réçu lors de la validation du formulaire
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PasswordRequirements) {
            throw new UnexpectedTypeException($constraint, PasswordRequirements::class);
        }

        // Si c'est pas null ou vide
        if (null === $value || '' === $value) {
            return;
        }

        // Si c'est pas une chaine de caractère
        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        // Vérification du mot de passe 8 caractères, avec au moins une lettre, un chiffre et un symbole
        if (!preg_match('/^.*^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
