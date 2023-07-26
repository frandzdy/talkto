<?php

namespace App\Enum;

/**
 * Status de validation du check in ou out
 */
enum CheckinValidateStatus: int
{
    case VALIDATE = 1;
    case VALIDATE_WITH_WARNING = 2;

    public static function getAvailableCheckinValidateStatus(): array
    {
        return [
            self::VALIDATE->value => 'Validé',
            self::VALIDATE_WITH_WARNING->value => 'Validé avec signalement',
        ];
    }

    /**
     * Affiche le label de l'item
     */
    public function label(): ?string
    {
        return self::getAvailableCheckinValidateStatus()[$this->value] ?? null;
    }
}
