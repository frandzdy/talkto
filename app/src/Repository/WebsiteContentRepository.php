<?php

namespace App\Repository;

use App\Entity\WebsiteContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WebsiteContent>
 *
 * @method null|WebsiteContent find($id, $lockMode = null, $lockVersion = null)
 * @method null|WebsiteContent findOneBy(array $criteria, array $orderBy = null)
 * @method WebsiteContent[]    findAll()
 * @method WebsiteContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WebsiteContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteContent::class);
    }

    //    /**
    //     * @return WebsiteContent[] Returns an array of WebsiteContent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WebsiteContent
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
