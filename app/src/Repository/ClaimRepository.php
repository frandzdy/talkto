<?php

namespace App\Repository;

use App\Entity\Claim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Claim>
 *
 * @method null|Claim find($id, $lockMode = null, $lockVersion = null)
 * @method null|Claim findOneBy(array $criteria, array $orderBy = null)
 * @method Claim[]    findAll()
 * @method Claim[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClaimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Claim::class);
    }

    public function save(Claim $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Claim $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Claim[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Claim
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
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
            ->createQueryBuilder('c')
        ;

        if (!empty($filters['term'])) {
            $builder
                ->join('c.checkin', 'checkin')
                ->addSelect('checkin')
                ->join('checkin.author', 'author')
                ->addSelect('author')
                ->join('checkin.transactionLine', 'transactionLine')
                ->addSelect('transactionLine')
                ->join('transactionLine.transaction', 'transaction')
                ->addSelect('transaction')
                ->join('transactionLine.product', 'product')
                ->addSelect('product')
                ->andWhere('author.email LIKE :term OR author.firstname LIKE :term OR author.lastname LIKE :term')
                ->orWhere('transaction.reference LIKE :term OR product.title LIKE :term')
                ->andWhere('product.deletedAt IS NULL')
                ->setParameter('term', $filters['term'].'%')
            ;
        }

        $builder->orderBy('c.id');

        return $builder->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
        ;
    }

    /**
     * Retourne la liste des réclamations en cours.
     */
    public function getClaims(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.checkin', 'checkin')
            ->addSelect('checkin')
            ->join('checkin.transactionLine', 'transactionLine')
            ->addSelect('transactionLine')
            // ->where('c.status = :claimStatus')
            // ->setParameter('claimStatus', ClaimStatus::PENDING)
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }
}
