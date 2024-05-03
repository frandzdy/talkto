<?php

namespace App\Autocompleter;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'product'])]
class ProductAutocompleter implements EntityAutocompleterInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack
    ) {}

    public function getEntityClass(): string
    {
        return Product::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        $user = $this->security->getUser();
        if ($user instanceof UserInterface) {
            $lat = $user->getLat();
            $lon = $user->getLon();
        } else {
            $lat = $this->requestStack->getSession()->get('lat', 46.227638);
            $lon = $this->requestStack->getSession()->get('lon', 2.213749);
        }

        return $repository
            // the alias "food" can be anything
            ->createQueryBuilder('p')
            ->select(
                'p,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->innerJoin('p.author', 'a')
            ->andWhere(
                'p.title LIKE :search OR p.description LIKE :search OR p.shortDescription LIKE :search OR p.amount LIKE :search 
            OR a.lastname LIKE :search OR a.firstname LIKE :search OR a.city LIKE :search OR a.zipCode LIKE :search'
            )
            ->setParameter('search', '%'.$query.'%')

            // maybe do some custom filtering in all cases
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', $lat ? 100 : 1000)
            ->andWhere('p.status = :productStatus')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->andWhere('a.isStripeAccountActive = :active')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('active', true)
            ->setParameter(':userLat', $lat ?: 46.227638)
            ->setParameter(':userLon', $lon ?: 2.213749)
            ->orderBy('p.title, p.amount', 'ASC')
            ->groupBy('p.id')
        ;
    }

    public function getLabel(array|object $entity): string
    {
        return $entity[0]->getTitle().' - '.$entity[0]->getAmount().' €';
    }

    public function getValue(array|object $entity): string
    {
        return $entity[0]->getToken();
    }

    // see the "security" option for details
    public function isGranted(Security $security): bool
    {
        return true;
    }

    public function getGroupBy(): mixed
    {
        return null;
    }
}
