<?php

namespace App\Controller\Front;

use App\Entity\Check;
use App\Entity\Message;
use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\CheckStatus;
use App\Form\CheckType;
use App\Repository\CheckRepository;
use App\Repository\ReservationRepository;
use App\Service\ChatService;
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

class ChatController extends AbstractController
{
    #[Route('/chat/{token}/{transactionLineToken}', name: 'chat_index', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
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
        $destinataireNotification = $user === $reservation->getAuthor() ? $transactionLine->getProduct()->getAuthor(
        )->getEmail() : $reservation->getAuthor()->getEmail();
        $rented = $reservation->getAuthor();
        $lessor = $transactionLine->getProduct()->getAuthor();
        if (!$messages) {
            $support = $em->getRepository(User::class)->findOneBy(['roles' => User::ROLE_SUPPORT]);
            $support1 = $em->getRepository(User::class)->findOneBy(['id' => 1]);

            $message = (new Message())
                ->setAuthor($support ?: $support1)
                ->setReservation($reservation)
                ->setMessage(
                    "Bienvenu de votre chat. 
                    Vous pouvez contacter votre locataire ou bailleur, afin de convenir d'un rendez-vous.
                    L'équipe support Rented."
                );
            $em->persist($message);
            $em->flush();
            // envoyer une notification pour le message
            $mailerManager->sendMailNotification(
                $destinataireNotification,
                'front/emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $user
                ]
            );
            $mailerManager->sendMailNotification(
                $destinataireNotification,
                'front/emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $reservation->getAuthor()
                ]
            );
            $messages[] = [$message];
        }
        $submittedToken = $request->request->get('_token');
        $message = $request->request->get('message');
        $error = false;

        if ($message && $this->isCsrfTokenValid('addMessage' . $user->getId(), $submittedToken)) {
            $message = (new Message())
                ->setMessage($message)
                ->setAuthor($user)
                ->setReservation($reservation);
            $em->persist($message);
            $em->flush();

            $mailerManager->sendMailNotification(
                $destinataireNotification,
                'front/emails/notification_message.html.twig',
                [
                    'message' => $message->getMessage(),
                    'sender' => $user
                ]
            );

            $messages = $em->getRepository(Message::class)->findBy(['reservation' => $reservation]);
        } elseif ($this->isCsrfTokenValid('addMessage' . $user->getId(), $submittedToken) && !$message) {
            return $this->json(
                [
                    'error' => 'Message nécessaire.'
                ]
            );
        }

        return $this->render(
            'front/chat/index.html.twig',
            compact(
                'messages',
                'user',
                'token',
                'transactionLineToken',
                'error',
                'product',
                'rented',
                'lessor'
            )
        );
    }
}
