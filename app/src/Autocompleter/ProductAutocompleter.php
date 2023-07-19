<?php

namespace App\Autocompleter;

use App\Entity\Product;
use App\Enum\ProductStatus;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

#[AutoconfigureTag('ux.entity_autocompleter', ['alias' => 'product'])]
class ProductAutocompleter implements EntityAutocompleterInterface
{
    public function __construct(private \Symfony\Bundle\SecurityBundle\Security $security, private RequestStack $requestStack)
    {
    }

    public function getEntityClass(): string
    {
        return Product::class;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        $user = $this->security->getUser();
        $lat = $this->requestStack->getSession()->get('lat', 0);
        $lon = $this->requestStack->getSession()->get('lon', 0);

        $qb = $repository
            // the alias "food" can be anything
            ->createQueryBuilder('p')
            ->select('p,
                    ( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat)))) AS distance')
            ->innerJoin('p.author', 'a')
            ->andWhere('p.title LIKE :search OR p.description LIKE :search OR p.amount LIKE :search')
            ->setParameter('search', '%'.$query.'%')

            // maybe do some custom filtering in all cases
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', 1000)
            ->andWhere('p.status = :productStatus')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->andWhere('a.isStripeAccountActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.title, p.amount', 'ASC')
            ;

        if ($user) {
            $qb->setParameter(':userLat', $user->getLat())
                ->setParameter(':userLon', $user->getLon());
        } else {
            $qb->setParameter(':userLat', $lat != 0 ? $lat : 48.866667)
                ->setParameter(':userLon', $lon != 0 ? $lon : 2.333333);
        }

        return $qb;
    }

    public function getLabel(array|object $entity): string
    {
        return $entity[0]->getTitle() . ' - ' . $entity[0]->getAmount() . ' €';
    }

    public function getValue(array|object $entity): string
    {
        return $entity[0]->getToken();
    }

    public function isGranted(Security $security): bool
    {
        // see the "security" option for details
        return true;
    }
}
