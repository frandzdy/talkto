<?php

namespace App\Twig\Runtime;

use App\Entity\Claim;
use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ProductCategory;
use App\Service\StripeManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class RentedExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private UserManager $userManager,
        private EntityManagerInterface $em,
        private StripeManager $stripeManager
    ) {}

    /**
     * Retourne la liste des catégories des produits afin de l'afficher par tout dans les vues Twig.
     */
    public function getProductCategories(): array
    {
        return ProductCategory::getUriLabels();
    }

    /**
     * Retourne le nombre de réclamations dans twig.
     */
    public function numberClaims(): int
    {
        return \count($this->em->getRepository(Claim::class)->getClaims());
    }

    /**
     * Retourne le nombre de réclamations dans twig.
     */
    public function numberProductToValidate(): int
    {
        return $this->em->getRepository(Product::class)->getProductToValidate();
    }

    /**
     * Retourne entre deux personnes.
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

    public function getInvoiceLink(Reservation $reservation): ?string
    {
        return $this->stripeManager->getInvoice($reservation->getTransaction());
    }

    public function getAccountLink(User $lessor): ?string
    {
        return $this->stripeManager->getAccountLink($lessor)->url;
    }

    public function getNumberReservationOnProgress(User $user)
    {
        return $this->em->getRepository(Reservation::class)->getUserReservations($user);
    }
}
