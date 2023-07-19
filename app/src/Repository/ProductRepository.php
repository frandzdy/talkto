<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ProductCategory;
use App\Enum\ProductStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function PHPUnit\Framework\matches;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param User $user
     * @param array $filter
     * @return QueryBuilder
     */
    public function getFilteredProducts(User $user, array $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    
                    ( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat)))) AS distance'
            )
            ->innerjoin('p.author', 'a')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andWhere('p.category = :productCategory')
            ->andWhere('p.amount BETWEEN :startAmount AND :endAmount')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':startDistance', $filter['startDistance'])
            ->setParameter(':endDistance', $filter['endDistance'])
            ->setParameter(':startAmount', $filter['startAmount'])
            ->setParameter(':endAmount', $filter['endAmount'])
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':productCategory', $filter['category'])
            ->setParameter(':userLat', $user->getLat())
            ->setParameter(':userLon', $user->getLon());

        match ((int)$filter['sortedBy']) {
            1 => $qb->orderBy('distance', 'ASC'),
            2 => $qb->orderBy('p.amount', 'ASC'),
            3 => $qb->orderBy('p.amount', 'DESC'),
            4 => $qb->orderBy('p.title', 'ASC'),
            5 => $qb->orderBy('p.title', 'DESC'),
        };

        return $qb;
    }
}
