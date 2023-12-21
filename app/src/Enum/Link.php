<?php

namespace App\Enum;

/**
 * Lien vers différentes vues.
 */
enum Link: int
{
    case LINK_LESSOR = 1;
    case LINK_RENTER = 2;
    case LINK_ABOUT = 3;

    public static function getLabels(): array
    {
        return [
            Link::LINK_LESSOR->value => 'front_seller_new',
            Link::LINK_RENTER->value => 'front_user_new',
            Link::LINK_ABOUT->value => 'front_aboutus',
        ];
    }

    /**
     * Affiche le label de l'item.
     */
    public function label(): ?string
    {
        return self::getLabels()[$this->value] ?? null;
    }
}
