<?php
    
    
    namespace App\Service;
    
    
    use App\Entity\Discussion;
    use App\Entity\Media;
    use App\Entity\Message;
    use App\Entity\RefTypeGroupe;
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use phpDocumentor\Reflection\Types\Boolean;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Validator\Constraints\File;
    use Symfony\Component\Validator\Validation;
    use Symfony\Component\Validator\Validator\ValidatorInterface;
    
    class ChatService
    {
        /**
         * ChatService constructor.
         */
        public function __construct(
            private EntityManagerInterface $em,
            private FileUploadManager $fileUploadManager,
            private UrlGeneratorInterface $generator,
            private ValidatorInterface $validation,
            private array $fileUploadParameters
        ) {}
        
        /**
         * Enregistre un message du chat
         */
        public function saveMessage(Discussion $discussion, string $message, User $from, $medias): ?Message
        {
            $msg = (new Message())
                ->setMessage($message)
                ->setSender($from)
                ->setCreatedAt((new \DateTime()))
                ->setAuthor($from)
                ->setDiscussion($discussion)
            ;

            if ($medias) {
                foreach ($medias as $media) {
                    $violations = $this->validation->validate(
                        $media,
                        [
                            new File(
                                [
                                    'maxSize' => "1024k",
                                    'mimeTypes' => ["video/*", "image/*"],
                                    'mimeTypesMessage' => "Veuillez charger un média au bon format. {{types}}",
                                ]
                            ),
                        ]
                    );
                    if (count($violations) === 0) {
                        $med = new Media();
                        $fileName = $this->fileUploadManager->uploadFile('medias', $media);
                        $med->setName($fileName);
                        $msg->addMedia($med);
                    }
                }
            }
            
            $this->em->persist($msg);
            $this->em->flush();
            
            return $msg;
        }
        
        /**
         * Retourne la discussion du chat
         */
        public function getDiscussion(string $groupeSearchingToken, User $me, int $step): array
        {
            $interlocuteur = null;
            $discussion = $this->em->getRepository(Discussion::class)->getDiscussionForChat($groupeSearchingToken);
            if ($discussion) {
                foreach ($discussion->getUsers() as $u) {
                    if ($u->getId() !== $me->getId()) {
                        $interlocuteur = $u;
                    }
                }
                
                return [
                    'res' => $this->em->getRepository(Message::class)->getAllMessageFromGroupe(
                        $discussion,
                        $step
                    ),
                    'interlocuteur' => [
                        'interlocuteurName' => $interlocuteur->getFirstname(),
                        'interlocuteurToken' => $interlocuteur->getToken(),
                        'interlocuteurAvatar' => $interlocuteur->getPrincipalPicture()
                            ? $this->fileUploadParameters['base_path_twig'] . $interlocuteur->getPrincipalPicture()
                            : '',
                        'interlocuteurProfile' => $this->generator->generate(
                            'user_show_friend',
                            ['tokenId' => $interlocuteur->getToken()]
                        )
                    ],
                ];
            }
            
            return ['res' => [], 'groupeId' => null];
        }
    }
