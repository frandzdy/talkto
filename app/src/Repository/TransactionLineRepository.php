<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Enum\ProductStatus;
use App\Enum\TransactionLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;

/**
 * @extends ServiceEntityRepository<TransactionLine>
 *
 * @method null|TransactionLine find($id, $lockMode = null, $lockVersion = null)
 * @method null|TransactionLine findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionLine[]    findAll()
 * @method TransactionLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionLine::class);
    }

    public function save(TransactionLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TransactionLine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getTopSales(?float $lat, ?float $lon)
    {
        $qb = $this->createQueryBuilder('tl')
            ->select(
                '
                    tl,
                    ceil(( 6372 * acos(cos(radians(:userLat)) * cos(radians(a.lat))
                       * cos(radians(a.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin(radians(a.lat))))) AS distance,
                       count(Distinct(p.id)) as nbSales
                    '
            )
            ->join('tl.product', 'p')
            ->addSelect('p')
            ->join('p.author', 'a')
            ->addSelect('a')
            ->groupBy('p.id')
            ->where('p.status = :productStatus')
            ->andWhere('a.isStripeAccountActive = true')
            ->andHaving('distance BETWEEN :startDistance AND :endDistance')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', $lat ? 100 : 1000)
            ->setParameter(':userLat', $lat ?: 46.227638)
            ->setParameter(':userLon', $lon ?: 2.213749)
        ;

        return $qb->getQuery()->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }

    /**
     * Vérifie si une transaction est en cours.
     */
    public function productHaveTransactionInProgress(Product $product): bool
    {
        $qb = $this->createQueryBuilder('tl')
            ->join('tl.product', 'p')
            ->addSelect('p')
            ->join('p.author', 'a')
            ->addSelect('a')
            ->where('tl.startDate <= :dateNow AND tl.endDate >= :dateNow')
            ->setParameter('dateNow', (new \DateTime())->format('Y-m-d'))
            ->andWhere('p.id = :productId')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('productId', $product->getId())
        ;

        $count = (clone $qb)->select('count(Distinct(tl.id))')
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * Vérifie si une transaction est en cours.
     */
    public function productCheckQuantityAvailable(Product $product, DatePoint $startDate): array
    {
        return $this->getEntityManager()->getRepository(Transaction::class)
            ->createQueryBuilder('t')
            ->select('t')
            ->join('t.transactionLines', 'tl')
            ->addSelect('tl')
            ->join('tl.product', 'p')
            ->addSelect('p')
            ->where('tl.startDate <= :date AND tl.endDate >= :date')
            ->setParameter('date', $startDate->format('Y-m-d'))
            ->andWhere('p.id = :product')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('product', $product->getId())
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }

    /**
     * Vérifie les statistiques financiers des transactions.
     */
    public function getStatTransactionLine(?DatePoint $date = null): array
    {
        $qb = $this->createQueryBuilder('tl')
            ->select('SUM(tl.fees) as profit,
            SUM(tl.amountTtc + tl.fees) as ca')
            ->where('tl.status IN (:tlStatus)')
            ->setParameter('tlStatus', [TransactionLineStatus::IN_PROGRESS->value, TransactionLineStatus::FINISHED->value])
        ;

        if ($date instanceof DatePoint) {
            $qb->join('tl.transaction', 't')
                ->andWhere('t.createdAt = :date')
                ->setParameter('date', $date)
            ;
        }

        return $qb->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }
}
