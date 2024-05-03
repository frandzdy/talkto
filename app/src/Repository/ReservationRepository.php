<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;

/**
 * @method null|Reservation find($id, $lockMode = null, $lockVersion = null)
 * @method null|Reservation findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    // /**
    //  * @return Picture[] Returns an array of Picture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Picture
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getProducts(User $user, int $offset): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $user->getId())
        ;

        $count = (clone $qb)->select('count(Distinct(r.id))')
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getSingleScalarResult()
        ;

        $qb->select('r')
            // ->orderBy('p.', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5)
        ;

        return [
            'results' => $qb->getQuery()->enableResultCache(3600)
                ->setQueryCacheLifetime(3600)
                ->setResultCacheLifetime(3600)
                ->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    public function getAvailableProducts(string $token): array
    {
        $maxDate = (new DatePoint('+1 year'))->format('Y-m-d');

        return $this->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->join('t.transactionLines', 'tl')
            ->join('tl.product', 'p')
            ->where('p.token = :token')
            ->andWhere('tl.endDate <= :endDate')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('token', $token)
            ->setParameter('endDate', $maxDate)
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getResult()
        ;
    }

    public function getUserReservations(User $user): null|bool|float|int|string
    {
        return $this->createQueryBuilder('r')
            ->select('count(Distinct(r.id))')
            ->innerJoin('r.author', 'author')
            ->where('author.id = :id')
            ->setParameter('id', $user->getId())
            ->andWhere('r.status IN (:status)')
            ->setParameter('status', [ReservationStatus::IN_PROGRESS, ReservationStatus::PENDING])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
