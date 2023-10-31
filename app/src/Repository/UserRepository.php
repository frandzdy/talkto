<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
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
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
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
     * Retourne la liste des produits d'un bailleur
     */
    public function getProducts(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->join('p.author', 'a')
            ->where('a.id = :userId')
            ->setParameter('userId', $user->getId())
        ;

        $count = (clone $qb)->select('count(Distinct(p.id))')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5);

        return [
            'results' => $qb->getQuery()->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * Retourne la liste des réservations ou locations d'un utilisateur selon son profil
     */
    public function getReservations(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->leftjoin('t.transactionLines', 'tl')
            ->join('tl.product', 'p');
        if (in_array(User::ROLE_USER, $user->getRoles())) {
            $qb->where('r.author = :userId');
        } else {
            $qb->join('p.author', 'u', Join::WITH, 'u.id = :userId');
        }
        $qb->setParameter('userId', $user->getId());

        $count = (clone $qb)->select('count(Distinct(r.id))')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('r')
            ->orderBy('tl.startDate, tl.status', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5);

        return [
            'results' => $qb->getQuery()->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * Retourne la liste des favoris d'un utilisateur
     */
    public function getWishlists(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Wishlist::class)
            ->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->join('w.product', 'p')
            ->join('p.author', 'author')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());

        $count = (clone $qb)->select('count(Distinct(w.id))')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('w')
            ->orderBy('w.createdAt', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5);

        return [
            'results' => $qb->getQuery()->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }

    /**
     * On récupère les locations uniquement pour les bailleurs
     */
    public function getRents(User $user, int $offset): array
    {
        $qb = $this->_em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->join('r.transaction', 't')
            ->leftjoin('t.transactionLines', 'tl')
            ->join('tl.product', 'p')
            ->join('r.author', 'author')
            ->where('author = :userId')
            ->setParameter('userId', $user->getId());

        $count = (clone $qb)->select('count(Distinct(r.id))')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('r')
            ->orderBy('tl.startDate, tl.status', 'DESC')
            ->setFirstResult($offset * 5)
            ->setMaxResults(5);

        return [
            'results' => $qb->getQuery()->getResult(),
            'totalPage' => ceil($count / 5),
            'page' => $offset + 1,
        ];
    }
}
