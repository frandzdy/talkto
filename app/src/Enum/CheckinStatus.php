<?php

namespace App\Enum;

use phpDocumentor\Reflection\Types\Self_;

/**
 * Status du checkin
 */
enum CheckinStatus: int
{
    case IN = 1;
    case OUT = 2;

    public static function getAvailableCheckinStatus(): array
    {
        return [
            self::IN->value => 'Checkin',
            self::OUT->value => 'Checkout',
        ];
    }

    /**
     * Affiche le label de l'item
     */
    public function label(): ?string
    {
        return self::getAvailableCheckinStatus()[$this->value] ?? null;
    }
}
