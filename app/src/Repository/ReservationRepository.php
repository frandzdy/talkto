<?php

namespace App\Repository;

use App\Entity\Picture;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatus;
use App\Enum\TransactionLineStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
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
            ->setParameter('userId', $user->getId());

        $count = (clone $qb)->select('count(Distinct(r.id))')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('r')
            //->orderBy('p.', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5);

        return [
            'results' => $qb->getQuery()->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * @param string $token
     * @return array
     */
    public function getAvailableProducts(string $token): array
    {
        $maxDate = new DatePoint('+1 year');

        return $this->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->join('t.transactionLines', 'tl')
            ->join('tl.product', 'p')
            ->where('p.token = :token')
            ->andWhere('r.status IN (:reservationStatus)')
            ->andWhere('tl.status IN (:transactionLineStatus)')
            ->andWhere('tl.endDate <= :endDate')
            ->setParameter('token', $token)
            ->setParameter('reservationStatus', [ReservationStatus::PENDING->value, ReservationStatus::IN_PROGRESS->value])
            ->setParameter(
                'transactionLineStatus',
                [TransactionLineStatus::WAITING->value, TransactionLineStatus::IN_PROGESS->value]
            )
            ->setParameter('endDate', $maxDate)
            ->getQuery()
            ->getResult();
    }
}
