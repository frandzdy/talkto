<?php

namespace App\Repository;

use App\Entity\Contributor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repo des contributeurs.
 *
 * @method null|Contributor find($id, $lockMode = null, $lockVersion = null)
 * @method null|Contributor findOneBy(array $criteria, array $orderBy = null)
 * @method Contributor[]    findAll()
 * @method Contributor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributorRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contributor::class);
    }

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
                ->andWhere('c.email LIKE :term OR c.fullname LIKE :term')
                ->setParameter('term', $filters['term'].'%')
            ;
        }

        $builder->orderBy('c.fullname', 'ASC');

        return $builder->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
        ;
    }
}
