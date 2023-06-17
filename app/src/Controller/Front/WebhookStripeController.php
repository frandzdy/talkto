<?php

namespace App\Controller\Front;

use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatus;
use App\Enum\TransactionStatus;
use App\Repository\TransactionRepository;
use App\Service\MailerManager;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookStripeController extends AbstractController
{
    #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function checkoutSession(
        Request                $request,
        StripeManager          $stripeManager,
        TransactionRepository  $transactionRepository,
        EntityManagerInterface $em,
        MailerManager          $mailer,
        array                  $stripeParameters
    ): Response
    {
        $endpoint_secret = $stripeParameters['wh_secret_key'];

        $payload = $request->getContent();
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('⚠️  Webhook error while parsing basic request.', 400);
        }

        if ($endpoint_secret) {
            // Only verify the event if there is an endpoint secret defined
            // Otherwise use the basic decoded event
            // Je récupère la signature
            $header = 'Stripe-Signature';
            $sig_header = $request->headers->get($header);
            //$sig_header = $request->server->get('HTTP_STRIPE_SIGNATURE'); //$_SERVER['HTTP_STRIPE_SIGNATURE'];
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                // Invalid signature
                echo '⚠️  Webhook error while validating signature.';
                return new Response('⚠️  Webhook error while validating signature.', 400);
            }
        }

        // Handle the event
        switch ($event->type) {
            /**
             * @var User $user
             */
            case 'payment_intent.succeeded':
                $session = $event->data->object;
                $paymentIntent = $stripeManager->retrievePaymentIntent($session->id);
                $transaction = $transactionRepository->findOneBy(['paymentIntentId' => $paymentIntent->id]);
                $transaction->setStatus(TransactionStatus::VALIDATE);
                // on fait un virement au
                $stripeManager->captureAndTransferPaymentIntent($paymentIntent, $transaction);
                $reservation = new Reservation();
                $reservation->setTransaction($transaction)
                    ->setToken(hash('sha256', random_bytes(32)))
                    ->setStatus(ReservationStatus::VALIDATE);
                $em->persist($reservation);
                $em->flush();
//                        $mailer->sendMailNotification(
//                            $user->getEmail(),
//                            'front/emails/create_account_abonnement_login_link.html.twig',
//                            [
//                                'link' => $loginLinkDetails->getUrl(),
//                                'user' => $user,
//                                'expiresAt' => $loginLinkDetails->getExpiresAt()
//                            ]
//                        );
                break;
            case 'payment_intent.canceled':
                $session = $event->data->object;
                $paymentIntent = $stripeManager->retrievePaymentIntent($session->id);
                $transaction = $transactionRepository->findOneBy(['paymentIntentId' => $paymentIntent->id]);
                $transaction->setStatus(TransactionStatus::REJECTED);
                $em->flush();
                //                        $mailer->sendMailNotification(
//                            $user->getEmail(),
//                            'front/emails/create_account_abonnement_login_link.html.twig',
//                            [
//                                'link' => $loginLinkDetails->getUrl(),
//                                'user' => $user,
//                                'expiresAt' => $loginLinkDetails->getExpiresAt()
//                            ]
//                        );
                break;
            // Unhandled event type
            default:
                // Unexpected event type
                return new Response('non', Response::HTTP_BAD_REQUEST);
        }

        return new Response('', Response::HTTP_OK);
    }
}
