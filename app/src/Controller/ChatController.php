<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\User;
use App\Service\ChatService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

#[Route("/chat", name: "chat_")]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class ChatController extends AbstractController
{
    #[Route("/", name:"show")]
    public function index()
    {
        return $this->render('chat/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    
    #[Route("/save-message/{discussion}/{tokenId}", name:"save_message", options:["expose"=>true], methods: ["POST"])]
    public function saveChatMessage(
        string $discussion,
        string $tokenId,
        Request $request,
        EntityManagerInterface $em,
        ChatService $chatService,
        NotificationService $notificationService
    ) :Response {
        if ($request->isMethod("POST")) {
            
            $message = (string)$request->request->get('message');
            $messageSanitaze = (string)$request->request->get('messageSanitaze');
            $medias = $request->files->get('media');
           
            $user = $em->getRepository(User::class)->findOneBy(
                ['token' => $tokenId]
            );
       
            $discussion = $em->getRepository(Discussion::class)->findOneBy(
                ['token' => $discussion]
            );
 
            if ($msg = $chatService->saveMessage($discussion, !empty($messageSanitaze) ? $messageSanitaze : $message, $user, $medias)) {
                $tokenUsers = [];
                foreach ($discussion->getUsers() as $dUser) {
                    $tokenUsers[] = $dUser->getToken();
                }

                $res = [];
                preg_match_all('/@\[([^\]]+)\]\(([^ \)]+)\)/', $message, $res);

                $notificationService->sendNotificationChat($res[2], $msg);
                $arrayMedias = [];
                foreach ($msg->getMedias() as $media) {
                    $arrayMedias[] = $media->getName();
                }
                return new JsonResponse(
                    [
                    'success' => true,
                    'resultat' => [
                        'groupId' => $discussion->getToken(),
                        'groupUsers' => $tokenUsers,
                        'message' => $msg->getMessage(),
                        'medias' => $arrayMedias,
                        'createdAt' => $msg->getCreatedAtFormatted(),
                        'id' => $user->getToken(),
                        'lastname' => $user->getFirstname(),
                        'firstname' => $user->getLastname(),
                        'email' => $user->getEmail(),
                        'avatar' => $user->getPrincipalPicture(),
                        'token' => $user->getToken()
                    ]
                ], Response::HTTP_OK);
            }
        }

        return new JsonResponse(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    #[Route("/load_message", name:"load_message", options:["expose"=>true], methods: ["POST"])]
    public function loadMessage(Request $request, ChatService $chatService)
    {
        if ($request->isMethod("POST")) {
            $groupeSearching = (string)$request->request->get('groupe') ?: 0;
            $step = (int)$request->request->get('step') ?: 0;
            
            if ($res = $chatService->getDiscussion($groupeSearching, $this->getUser(), $step)) {

                return new JsonResponse(
                    [
                        'success' => true,
                        'resultat' => $res['res'],
                        'interlocuteur' => $res['interlocuteur']
                    ],
                    Response::HTTP_OK
                );
            }
        }

        return new JsonResponse(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
