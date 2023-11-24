<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\ProductCategory;
use App\Enum\ProductStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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
     * @param array $filter
     * @return QueryBuilder
     */
    public function getFilteredProducts(array $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->innerjoin('p.author', 'a')
            ->addSelect('a')
            ->leftjoin('p.pictures', 'pictures')
            ->addSelect('pictures')
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
            ->setParameter(':userLat', $filter['lat'] ?: 48.866667)
            ->setParameter(':userLon', $filter['lon'] ?: 2.333333);

        match ((int)$filter['sortedBy']) {
            1 => $qb->orderBy('distance', 'ASC'),
            2 => $qb->orderBy('p.amount', 'ASC'),
            3 => $qb->orderBy('p.amount', 'DESC'),
            4 => $qb->orderBy('p.title', 'ASC'),
            5 => $qb->orderBy('p.title', 'DESC'),
        };

        return $qb;
    }

    public function searchProducts(array $filter): QueryBuilder
    {
        $searchIds = [$filter['searchIds']];
        if (is_array(explode(',', $filter['searchIds']))) {
            $searchIds = explode(',', $filter['searchIds']);
        }
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->innerjoin('p.author', 'a')
            ->leftJoin('p.pictures', 'pictures')
            ->addSelect('pictures')
            ->addSelect('a')
            ->where('p.status = :productStatus')
            ->andWhere('p.token IN (:searchIds)')
            ->andWhere('a.isStripeAccountActive = true')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':searchIds', $searchIds)
            ->setParameter(':startDistance', $filter['startDistance'])
            ->setParameter(':endDistance', $filter['endDistance'])
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':userLat', $filter['lat'] ?: 48.866667)
            ->setParameter(':userLon', $filter['lon'] ?: 2.333333);

        match ((int)$filter['sortedBy']) {
            1 => $qb->orderBy('distance', 'ASC'),
            2 => $qb->orderBy('p.amount', 'ASC'),
            3 => $qb->orderBy('p.amount', 'DESC'),
            4 => $qb->orderBy('p.title', 'ASC'),
            5 => $qb->orderBy('p.title', 'DESC'),
        };

        return $qb;
    }

    /**
     * Construit une requête de recherche
     */
    public function buildSearchQuery(array $filters = [], bool $isLessor = false): Query
    {
        $builder = $this
            ->createQueryBuilder('p')
        ->join('p.author', 'a');

        if (!empty($filters['term']) && !$isLessor) {
            $builder
                ->andWhere('p.title LIKE :term OR a.lastname LIKE :term OR a.firstname LIKE :term OR p.shortDescription LIKE :term OR p.description LIKE :term')
                ->setParameter('term', $filters['term'] . '%');
        }

        $builder->orderBy('p.title, p.createdAt', 'ASC');

        return $builder->getQuery();
    }

    public function getTrends(?int $lat, ?int $lon, ?ProductCategory $productCategory = null, ?int $maxResult = 8): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->join('p.author', 'a')
            ->join('p.pictures', 'pictures')
            ->addSelect('pictures')
            ->addSelect('a')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', 100)
            ->setParameter(':userLat', $lat ?: 48.866667)
            ->setParameter(':userLon', $lon ?: 2.333333)
            ->orderBy('p.numberView', 'ASC')
            ->setMaxResults($maxResult)
            ;

        if ($productCategory) {
            $qb->andWhere('p.category = :productCategory')
                ->setParameter('productCategory', $productCategory)
            ;
        }
        return $qb->getQuery()->getResult();
    }

    public function getLatestProducts(?int $lat, ?int $lon): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6371 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->join('p.author', 'a')
            ->join('p.pictures', 'pictures')
            ->addSelect('pictures')
            ->addSelect('a')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', 100)
            ->setParameter(':userLat', $lat ?: 48.866667)
            ->setParameter(':userLon', $lon ?: 2.333333)
            ->orderBy('p.createdAt', 'ASC')
            ->setMaxResults(10)
        ;

        return $qb->getQuery()->getResult();
    }
}
