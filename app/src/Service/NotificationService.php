<?php
    
    
    namespace App\Service;
    
    
    use App\Entity\Message;
    use App\Entity\Notification;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    
    class NotificationService
    {
        /**
         * @var EntityManagerInterface
         */
        private $em;
        
        /**
         * NotificationService constructor.
         * @param EntityManagerInterface $em
         */
        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }
        
        /**
         * @param array $tokens
         * @param Wall $wall
         * @param bool $forCommentaire
         * @return bool
         */
//        public function sendNotificationWall(array $tokens, Wall $wall, $forCommentaire = false)
//        {
//            if ($tokens) {
//                foreach ($tokens as $token) {
//                    $user = $this->em->getRepository(User::class)->findOneBy(
//                        [
//                            'token' => $token
//                        ]
//                    );
//                    if ($user) {
//                        $notification = new Notification();
//                        $notification->setUser($user)->setWall($wall)->setLu(false)->setCreatedAt((new \DateTime()));
//                        if ($forCommentaire) {
//                            $this->em->persist($notification);
//                        }
//                        $this->em->flush();
//                    }
//                }
//                return true;
//            }
//            return false;
//        }
        
        /**
         * @param array $tokens
         * @param Message $message
         * @return bool
         */
        public function sendNotificationChat(array $tokens, Message $message)
        {
            if ($tokens) {
                foreach ($tokens as $token) {
                    $user = $this->em->getRepository(User::class)->findOneBy(
                        [
                            'token' => $token
                        ]
                    );
                    if ($user) {
                        $notification = new Notification();
                        $notification->setUser($user)->setMessage($message)->setRead(false)->setCreatedAt(
                            (new \DateTime())
                        );
                        $this->em->flush();
                    }
                }
                return true;
            }
            return false;
        }
    }
