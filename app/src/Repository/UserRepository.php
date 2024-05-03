<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method null|User find($id, $lockMode = null, $lockVersion = null)
 * @method null|User findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Retourne la liste des produits d'un bailleur.
     */
    public function getProducts(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->select('p')
            ->join('p.author', 'a')
            ->addSelect('a')
            ->where('a.id = :userId')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('userId', $user->getId())
        ;

        $count = (clone $qb)->select('count(Distinct(p.id))')
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getSingleScalarResult()
        ;

        $qb->select('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5)
        ;

        return [
            'results' => $qb->getQuery()
                ->enableResultCache(3600)
                ->setQueryCacheLifetime(3600)
                ->setResultCacheLifetime(3600)->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * Retourne la liste des réservations ou locations d'un utilisateur selon son profil.
     */
    public function getReservations(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->addSelect('t')
            ->leftjoin('t.transactionLines', 'tl')
            ->addSelect('tl')
            ->join('tl.product', 'p')
        ;
        if (User::ROLE_USER === $user->getRole() || User::ROLE_GUESS === $user->getRole()) {
            $qb->where('r.author = :userId')
                ->andWhere('p.deletedAt IS NULL')
            ;
        } else {
            $qb->join('p.author', 'u', Join::WITH, 'u.id = :userId')
                ->andWhere('p.deletedAt IS NULL')
            ;
        }

        $qb->setParameter('userId', $user->getId());

        $count = (clone $qb)->select('count(Distinct(r.id))')
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getSingleScalarResult()
        ;

        $qb->select('r')
            ->orderBy('tl.startDate, tl.status', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5)
        ;

        return [
            'results' => $qb->getQuery()
                ->enableResultCache(3600)
                ->setQueryCacheLifetime(3600)
                ->setResultCacheLifetime(3600)
                ->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * Retourne la liste des favoris d'un utilisateur.
     */
    public function getWishlists(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Wishlist::class)
            ->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->addSelect('u')
            ->join('w.product', 'p')
            ->addSelect('p')
            ->join('p.author', 'author')
            ->addSelect('author')
            ->where('u.id = :userId')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('userId', $user->getId())
        ;

        $count = (clone $qb)->select('count(Distinct(w.id))')
            ->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
            ->getSingleScalarResult()
        ;

        $qb->select('w')
            ->orderBy('w.createdAt', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5)
        ;

        return [
            'results' => $qb->getQuery()
                ->enableResultCache(3600)
                ->setQueryCacheLifetime(3600)
                ->setResultCacheLifetime(3600)
                ->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * On récupère les locations uniquement pour les bailleurs.
     */
    public function getRents(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->addSelect('t')
            ->leftjoin('t.transactionLines', 'tl')
            ->addSelect('tl')
            ->join('tl.product', 'p')
            ->addSelect('p')
            ->join('r.author', 'author')
            ->addSelect('author')
            ->where('author.id = :userId')
            ->andWhere('p.deletedAt IS NULL')
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
            ->orderBy('tl.startDate, tl.status', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5)
        ;

        return [
            'results' => $qb->getQuery()
                ->enableResultCache(3600)
                ->setQueryCacheLifetime(3600)
                ->setResultCacheLifetime(3600)
                ->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * Construit une requête de recherche.
     */
    public function buildSearchQuery(array $filters = [], bool $isLessor = false): Query
    {
        $builder = $this
            ->createQueryBuilder('u')
        ;

        if (!empty($filters['term']) && !$isLessor) {
            $builder
                ->andWhere(
                    'u.email LIKE :term OR u.firstname LIKE :term OR u.lastname LIKE :term OR u.address LIKE :term OR u.city LIKE :term OR u.zipCode LIKE :term OR u.stripeAccountId LIKE :term OR u.phone LIKE :term'
                )
                ->setParameter('term', $filters['term'].'%')
            ;
        } elseif (!empty($filters['term'])) {
            $builder
                ->andWhere(
                    'u.email LIKE :term OR u.firstname LIKE :term OR u.lastname LIKE :term OR u.address LIKE :term OR u.city LIKE :term OR u.zipCode LIKE :term OR u.stripeAccountId LIKE :term OR u.stripeCustomerId LIKE :term OR u.phone LIKE :term'
                )
                ->setParameter('term', $filters['term'].'%')
            ;
        }

        $builder->andWhere('u.role IN (:role)')
            ->andWhere('u.deletedAt IS NULL')
        ;
        if ($isLessor) {
            $builder->andWhere('u.isStripeAccountActive = :status')
                ->setParameter('status', $filters['status'])
                ->setParameter('role', User::ROLE_SELLER)
            ;
        } else {
            $builder->setParameter('role', [User::ROLE_USER, User::ROLE_GUESS]);
        }

        $builder->orderBy('u.lastname, u.firstname', 'ASC');

        return $builder->getQuery()
            ->enableResultCache(3600)
            ->setQueryCacheLifetime(3600)
            ->setResultCacheLifetime(3600)
        ;
    }

    /**
     * Returns the statistics of users based on their role and creation date.
     */
    public function statsUsers(string $role, ?DatePoint $datePoint = null): array
    {
        $builder = $this
            ->createQueryBuilder('u')
            ->select('count(Distinct(u.id)) AS nbUsers')
            ->where('u.role = :role')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('role', $role)
        ;

        if ($datePoint instanceof DatePoint) {
            $builder->andWhere('u.createdAt = :date')
                ->setParameter('date', $datePoint)
            ;
        }

        return $builder->getQuery()->getResult();
    }

    public function getUserInactive(DatePoint $datePoint): array
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.lastDateConnexion like :date')
            ->setParameter('date', $datePoint->format('Y-m-d').'%')
            ->getQuery()
            ->getResult()
        ;
    }
}
