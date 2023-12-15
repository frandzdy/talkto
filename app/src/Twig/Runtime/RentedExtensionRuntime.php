<?php

namespace App\Twig\Runtime;

use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ProductCategory;
use App\Repository\ClaimRepository;
use App\Repository\ReservationRepository;
use App\Service\StripeManager;
use App\Service\UserManager;
use Twig\Extension\RuntimeExtensionInterface;

readonly class RentedExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private UserManager $userManager,
        private ClaimRepository $claimRepository,
        private StripeManager $stripeManager
    ) {
    }

    /**
     * Retourne la liste des catégories des produits afin de l'afficher par tout dans les vues Twig
     */
    public function getProductCategories(): array
    {
        return ProductCategory::getLabels();
    }

    /**
     * Retourne le nombre de réclamations dans twig
     */
    public function numberClaims(): int
    {
        return \count($this->claimRepository->getClaims());
    }

    /**
     * Retourne entre deux personnes
     */
    public function getDistance(User $renter, User $lessor): ?float
    {
        return $this->userManager->distance(
            $renter->getLat(),
            $renter->getLon(),
            $lessor->getLat(),
            $lessor->getLon()
        );
    }

    public function getInvoiceLink(Reservation $reservation)
    {
        return $this->stripeManager->getInvoice($reservation->getTransaction());
    }
}
