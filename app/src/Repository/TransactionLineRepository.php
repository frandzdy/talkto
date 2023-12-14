<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Enum\ProductStatus;
use App\Enum\TransactionLineStatus;
use App\Enum\TransactionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;

/**
 * @extends ServiceEntityRepository<TransactionLine>
 *
 * @method TransactionLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionLine|null findOneBy(array $criteria, array $orderBy = null)
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

    public function getTopSales(?int $lat, ?int $lon)
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
            ->setParameter(':productStatus', ProductStatus::VALIDATE)
            ->setParameter(':startDistance', 0)
            ->setParameter(':endDistance', 100)
            ->setParameter(':userLat', $lat ?: 48.866667)
            ->setParameter(':userLon', $lon ?: 2.333333);

        return $qb->getQuery()->getResult();
    }

    /**
     * Vérifie si une transaction est en cours
     */
    public function productHaveTransactionInProgress(Product $product): bool
    {
        $qb = $this->createQueryBuilder('tl')
            ->join('tl.product', 'p')
            ->addSelect('p')
            ->join('p.author', 'a')
            ->addSelect('a')
            ->where('tl.startDate <= :dateNow AND tl.endDate >= :dateNow')
            ->setParameter('dateNow', (new DatePoint())->format('Y-m-d'))
            ->andWhere('p.id = :productId')
            ->setParameter('productId', $product->getId());

        $count = (clone $qb)->select('count(Distinct(tl.id))')
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Vérifie si une transaction est en cours
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
            ->andWhere('t.status IN (:transactionStatus)')
            ->andWhere('tl.status IN (:transactionLineStatus)')
            ->andWhere('p.id = :product')
            ->setParameter('product', $product->getId())
            ->setParameter('transactionStatus', [TransactionStatus::WAITING->value, TransactionStatus::VALIDATE->value])
            ->setParameter('transactionLineStatus', TransactionLineStatus::IN_PROGRESS->value)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie les statistiques financiers des transactions
     */
    public function getStatTransactionLine(DatePoint $date = null): array
    {

        $qb = $this->createQueryBuilder('tl')
            ->select('SUM(tl.fees) as profit,
            SUM(tl.amountTtc + tl.fees) as ca');

        if ($date) {
            $qb->join('tl.transaction', 't')
                ->where('t.createdAt = :date')
                ->setParameter('date', $date)
            ;
        }

        return $qb->getQuery()
            ->getResult();
    }
}
