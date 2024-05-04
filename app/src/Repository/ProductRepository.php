<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enum\ProductCategory;
use App\Enum\ProductStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method null|Product find($id, $lockMode = null, $lockVersion = null)
 * @method null|Product findOneBy(array $criteria, array $orderBy = null)
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

    public function getFilteredProducts(array $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
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
            ->andWhere('p.deletedAt IS NULL')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->orHaving('distance IS NULL')
            ->setParameter(':startDistance', $filter['startDistance'])
            ->setParameter(':endDistance', $filter['endDistance'])
            ->setParameter(':startAmount', $filter['startAmount'])
            ->setParameter(':endAmount', $filter['endAmount'])
            ->setParameter(':productStatus', ProductStatus::VALIDATE->value)
            ->setParameter(':productCategory', $filter['category'])
            ->setParameter(':userLat', $filter['lat'] ?: 48.866667)
            ->setParameter(':userLon', $filter['lon'] ?: 2.333333)
        ;

        match ((int) $filter['sortedBy']) {
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
        $searchIds = explode(',', (string) $filter['searchIds']);

        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
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
            ->andWhere('p.deletedAt IS NULL')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':searchIds', $searchIds)
            ->setParameter(':startDistance', $filter['startDistance'])
            ->setParameter(':endDistance', $filter['endDistance'])
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':userLat', $filter['lat'] ?: 48.866667)
            ->setParameter(':userLon', $filter['lon'] ?: 2.333333)
        ;

        match ((int) $filter['sortedBy']) {
            1 => $qb->orderBy('distance', 'ASC'),
            2 => $qb->orderBy('p.amount', 'ASC'),
            3 => $qb->orderBy('p.amount', 'DESC'),
            4 => $qb->orderBy('p.title', 'ASC'),
            5 => $qb->orderBy('p.title', 'DESC'),
        };

        return $qb;
    }

    /**
     * Construit une requête de recherche.
     */
    public function buildSearchQuery(array $filters = []): Query
    {
        $builder = $this->createQueryBuilder('p')
            ->join('p.author', 'a')
        ;

        if (!empty($filters['term'])) {
            $builder
                ->andWhere(
                    'p.title LIKE :term OR a.lastname LIKE :term OR a.firstname LIKE :term OR p.shortDescription LIKE :term OR p.description LIKE :term'
                )
                ->setParameter('term', $filters['term'].'%')
            ;
        }

        $builder
            ->andWhere('p.status = :status')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('status', $filters['status']->value)
        ;

        $builder->orderBy('p.status, p.title, p.createdAt', 'ASC');

        return $builder->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
        ;
    }

    public function getTrends(
        ?float $lat,
        ?float $lon,
        ?ProductCategory $productCategory = null,
        ?int $maxResult = 8,
        ?Product $excludedProduct = null
    ): ?array {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->join('p.author', 'a')
            ->join('p.pictures', 'pictures')
            ->addSelect('pictures')
            ->addSelect('a')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andWhere('p.deletedAt IS NULL')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', $lat ? 100 : 1000)
            ->setParameter(':userLat', $lat ?: 46.227638)
            ->setParameter(':userLon', $lon ?: 2.213749)
            ->orderBy('p.numberView, distance', 'ASC')
            ->setMaxResults($maxResult)
        ;

        if ($productCategory instanceof ProductCategory) {
            $qb->andWhere('p.category = :productCategory')
                ->setParameter('productCategory', $productCategory)
            ;
        }

        if ($excludedProduct instanceof Product) {
            $qb->andWhere('p.id <> :excludedProduct')
                ->setParameter('excludedProduct', $excludedProduct)
            ;
        }

        return $qb->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }

    public function getLatestProducts(?float $lat, ?float $lon): ?array
    {
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
                    p,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance'
            )
            ->join('p.author', 'a')
            ->join('p.pictures', 'pictures')
            ->addSelect('pictures')
            ->addSelect('a')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andWhere('p.deletedAt IS NULL')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', $lat ? 100 : 1000)
            ->setParameter(':userLat', $lat ?: 46.227638)
            ->setParameter(':userLon', $lon ?: 2.213749)
            ->orderBy('p.createdAt, distance', 'ASC')
            ->setMaxResults(10)
        ;

        return $qb->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }

    /**
     * Retourne le nombre de produits en attente de validation.
     */
    public function getProductToValidate(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.status = :productStatus')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('productStatus', ProductStatus::WAITING)
        ;

        return (clone $qb)->select('count(Distinct(p.id))')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
