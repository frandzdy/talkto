<?php

namespace App\Controller\Front;

use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChatController extends AbstractController
{
    #[Route('/chat/{token}/{transactionLineToken}', name: 'chat_index', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function chat(
        string $token,
        string $transactionLineToken,
        EntityManagerInterface $em,
        Request $request,
        MailerManager $mailerManager
    ): Response {
        $reservation = $em->getRepository(Reservation::class)->findOneBy(['token' => $token]);
        $transactionLine = $em->getRepository(TransactionLine::class)->findOneBy(['token' => $transactionLineToken]);
        $product = $transactionLine->getProduct();
        $messages = $em->getRepository(Message::class)->findBy(['reservation' => $reservation]);
        $user = $this->getUser();
        $lessor = $transactionLine->getProduct()->getAuthor();
        $rented = $reservation->getAuthor();
        $receiverNotification = $user === $rented ? $lessor : $rented;
        $senderNotification = $user === $rented ? $rented : $lessor;
        if (!$messages) {
            $support = $em->getRepository(User::class)->findOneBy(['role' => User::ROLE_SUPPORT]);

            $message = (new Message())
                ->setAuthor($support)
                ->setReservation($reservation)
                ->setMessage(
                    "Bienvenue dans votre chat. 
                    Vous pouvez contacter votre locataire ou bailleur, afin de convenir d'un rendez-vous.
                    L'équipe support Reented."
                )
            ;
            $em->persist($message);
            $em->flush();
            $mailerManager->sendMailNotification(
                $lessor->getEmail(),
                'emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $rented,
                    'receiver' => $lessor,
                ]
            );
            // envoyer une notification pour le message
            $mailerManager->sendMailNotification(
                $rented->getEmail(),
                'emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $lessor,
                    'receiver' => $rented,
                ]
            );

            $messages[] = [$message];
        }

        $submittedToken = $request->request->get('_token');
        $message = $request->request->get('message');
        $error = false;
        $submit = false;
        if ($message && $this->isCsrfTokenValid('addMessage'.$user->getId(), $submittedToken)) {
            $message = (new Message())
                ->setMessage($message)
                ->setAuthor($user)
                ->setReservation($reservation)
            ;
            $em->persist($message);
            $em->flush();

            $mailerManager->sendMailNotification(
                $receiverNotification->getEmail(),
                'emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $senderNotification,
                    'receiver' => $receiverNotification,
                ]
            );
            $submit = true;
            $messages = $em->getRepository(Message::class)->findBy(['reservation' => $reservation]);
        } elseif ($this->isCsrfTokenValid('addMessage'.$user->getId(), $submittedToken) && !$message) {
            $error = true;
            $this->addFlash('error', 'Message nécessaire.');
        }

        return $this->render(
            'front/chat/index.html.twig',
            ['messages' => $messages, 'user' => $user, 'token' => $token, 'transactionLineToken' => $transactionLineToken, 'error' => $error, 'product' => $product, 'rented' => $rented, 'lessor' => $lessor, 'submit' => $submit]
        );
    }
}
