<?php
    
    namespace App\Repository;
    
    use App\Entity\Picture;
    use App\Entity\Reservation;
    use App\Entity\User;
    use App\Enum\ReservationStatus;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    
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
            $minDate = (new \DateTime('now'));
            $maxDate = (new \DateTime('now'))->modify('+1 year');

            return $this->createQueryBuilder('r')
                ->join('r.transaction', 't')
                ->join('t.transactionLines', 'tl')
                ->join('tl.product', 'p')
                ->where('p.token = :token')
                ->andWhere('r.status = :reservationStatus')
                ->andWhere('tl.startDate >= :startDate')
                ->andWhere('tl.endDate <= :endDate')

                ->setParameter('token', $token)
                ->setParameter('reservationStatus', ReservationStatus::VALIDATE->value)
                ->setParameter('startDate', $minDate)
                ->setParameter('endDate', $maxDate)
                ->getQuery()
                ->getResult();
        }
    }
