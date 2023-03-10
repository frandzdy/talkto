<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    /*
     * Retourne la liste des profils correspondant à certain critère lier à l'utilisateur
     */
    public function getMatchingUsers(User $user)
    {
        $unWanted = [];
        foreach ($user->getMyUnWantedMatchs() as $myUnWantedMatch) {
            $unWanted[] = $myUnWantedMatch->getId();
        }
        $wanted = [];
        foreach ($user->getMyMatchs() as $myMatch) {
            $wanted[] = $myMatch->getId();
        }
        $qb = $this->createQueryBuilder('u')
            ->select(
                '
                    u,
                    ( 6371 * acos( cos( radians(:userLat) ) * cos( radians( u.lat ) )
                       * cos( radians(u.lon) - radians(:userLon)) + sin(radians(:userLat))
                       * sin( radians(u.lat)))) AS distance'
            )
            ->setParameter(':userLat', $user->getLat())
            ->setParameter(':userLon', $user->getLon())
            // personnes autres que moi ;)
            ->where('u.id != :userId')
            ->setParameter(':userId', $user->getId())
            ->andWhere('u.hideAccount = :false')
            ->andWhere('u.hideAccount24 = :false')
            ->setParameter(':false', 0);
        // n'est pas dans la liste des unWantedMatch et les wantedMatch
        if ($unWanted) {
            $qb->andWhere('u.id NOT IN (:unWantedId)')
                ->setParameter(':unWantedId', $unWanted);
        }
        if ($wanted) {
            $qb->andWhere('u.id NOT IN (:wantedId)')
                ->setParameter(':wantedId', $wanted);
        }
        $qb
            ->andWhere('u.genre = :interestGenre')
            ->setParameter(':interestGenre', $user->getInterestGenre())
            ->andWhere('u.age >= :interestAge')
            ->setParameter(':interestAge', $user->getInterestAge())
            ->andWhere('(u.children = :interestChildren OR u.smoker = :interestSmoke)')
            ->setParameter(':interestSmoke', $user->getInterestSmoker())
            ->setParameter(':interestChildren', $user->getInterestChildren())
            ;
        // si abonné
        if ($user->getStripeStatus() === "active") {
            // les personnes qui sont parmis mes préférences d'intérêts
            $qb->andWhere('
                    (
                    u.interestExit IN (:interestExit) OR
                    u.interestHobbies IN (:interestHobbies) OR
                    u.interestSports IN (:interestSports)
                )')
                ->setParameter(':interestExit', $user->getInterestExit())
                ->setParameter(':interestHobbies', $user->getInterestHobbies())
                ->setParameter(':interestSports', $user->getInterestSports());
        }
        // ayant une distance inférieur ou égale à mes préférences de distance
        $qb->having('distance <= :interestDistance')
            ->setParameter(':interestDistance', $user->getInterestDistance());

        return $qb->getQuery()->getResult();
    }

    /*
    * Retourne la liste des profils correspondant à certain critère lier à l'utilisateur
    */
    public function getMatchingFromOtherUsers(User $user)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.myMatchs', 'myMatchs')
            // personnes autres que moi ;)
            ->where('u.id != :userId')
            // n'est pas dans la liste des unWantedMatch et les wantedMatch
            ->andWhere('myMatchs.id = :userId')
            ->setParameter('userId', $user->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * On récupère la liste des utilisateurs qui peuvent réinit leur compteur de spam
     */
    public function getUsersToReinitAccount()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('datediff(now(), u.lastUpdateNumberSpam) as diff_hours, u')
            ->where('u.lastUpdateNumberSpam IS NOT NULL')
            ->andWhere("u.stripeStatus = 'incomplete'")
            ->having('diff_hours = 0');

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * On récupère la liste des utilisateurs qui peuvent réinit leur compteur de spam
     */
    public function getUsersToReactivateAccount()
    {
        $qb = $this->createQueryBuilder('u')
            ->select('datediff(now(), u.hideAccountDate24) as diff_hours, u')
            ->where('u.hideAccountDate24 IS NOT NULL')
            ->andWhere('u.hideAccount24 = true')
            ->having('diff_hours = 0');

        return $qb->getQuery()->toIterable();
    }
}
