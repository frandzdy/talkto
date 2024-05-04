<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method null|Transaction find($id, $lockMode = null, $lockVersion = null)
 * @method null|Transaction findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
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

    /**
     * Construit une requête de recherche.
     */
    public function buildSearchQuery(array $filters = []): Query
    {
        $builder = $this
            ->createQueryBuilder('t')
            ->join('t.author', 'a')
            ->join('t.transactionLines', 'tl')
        ;

        if (!empty($filters['term'])) {
            $builder
                ->andWhere('t.reference LIKE :term OR a.lastname LIKE :term OR a.firstname LIKE :term')
                ->setParameter('term', '%'.$filters['term'].'%')
            ;
        }

        $builder
            ->andWhere('t.paymentIntentId IS NOT NULL')
        ;

        $builder->orderBy('t.reference', 'ASC');

        return $builder->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
        ;
    }
}
