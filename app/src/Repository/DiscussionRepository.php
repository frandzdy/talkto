<?php
    
    
    namespace App\Repository;
    
    
    use App\Entity\Discussion;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    class DiscussionRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Discussion::class);
        }
        
        
        /**
         * @param array $data
         * @param $type
         * @return mixed
         */
        public function getDiscussion(array $data)
        {
            $res = $this->createQueryBuilder('discussion')
                ->select('discussion');
            foreach ($data as $dat) {
                $res->join(
                    'discussion.users',
                    'users' . $dat->getId(),
                    'WITH',
                    'users' . $dat->getId() . '.id = ' . $dat->getId()
                );
            }
            $res->groupBy('discussion.id');
            
            return $res->getQuery()->getResult();
        }
        
        
        /**
         * Retourne la discussion du chat
         */
        public function getDiscussionForChat($token)
        {
            $res = $this->createQueryBuilder('discussion')
                ->select('discussion')
                ->leftJoin('discussion.messages', 'messages')
                ->addSelect('messages')
                ->leftJoin('discussion.users', 'users')
                ->addSelect('users')
                ->where('discussion.token = :token')
                ->setParameter('token', $token);

            return $res->getQuery()->getSingleResult();
        }
    }
