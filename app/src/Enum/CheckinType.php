<?php

namespace App\Enum;

/**
 * Type de checkin.
 */
enum CheckinType: int
{
    case IN = 1;
    case OUT = 2;

    public static function getAvailableCheckinType(): array
    {
        return [
            self::IN->value => 'Checkin',
            self::OUT->value => 'Checkout',
        ];
    }

    /**
     * Affiche le label de l'item.
     */
    public function label(): ?string
    {
        return self::getAvailableCheckinType()[$this->value] ?? null;
    }
}
