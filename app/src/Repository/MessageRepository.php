<?php


namespace App\Repository;


use App\Entity\Discussion;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Retourne tous les messages d"un groupe de discussion
     */
    public function getAllMessageFromGroupe(Discussion $discussion, $step = 0) :array
    {
        $result = [];
        $res = $this->createQueryBuilder('message')
            ->select('message')
            ->leftJoin('message.discussion', 'discussion')
            ->addSelect('discussion')
            ->leftJoin('message.sender', 'sender')
            ->addSelect('sender')
            ->leftjoin('message.medias', 'medias')
            ->addSelect('medias')
            ->where('discussion.token = :token')
            ->setParameter('token', $discussion->getToken())
            ->orderBy('message.id', 'DESC')
            ->setFirstResult($step)
            ->setMaxResults(5)
            ->getQuery()->getResult();
        
        foreach ($discussion->getUsers() as $dUser) {
            $tokenUsers[] = $dUser->getToken();
        }
        
        foreach ($res as $re) {
            $arrayMedias = [];
            foreach ($re->getMedias() as $media) {
                $arrayMedias[] = $media->getName();
            }
            $result[] = [
                'groupId' => $discussion->getToken(),
                'groupUsers' => $tokenUsers,
                'message' => $re->getMessage(),
                'messageId' => $re->getId(),
                'medias' => $arrayMedias,
                'createdAt' => $re->getCreatedAtFormatted(),
                'id' => $re->getSender()->getToken(),
                'lastname' => $re->getSender()->getFirstname(),
                'firstname' => $re->getSender()->getLastname(),
                'email' => $re->getSender()->getEmail(),
                'avatar' => $re->getSender()->getPrincipalPicture(),
                'token' => $re->getSender()->getToken()
            ];
           
        }
        
        return $result;
    }
}
