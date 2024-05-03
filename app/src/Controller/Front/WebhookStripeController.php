<?php

namespace App\Controller\Front;

use App\Entity\Reservation;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\ReservationStatus;
use App\Enum\TransactionStatus;
use App\Repository\TransactionRepository;
use App\Service\MailerManager;
use App\Service\StripeManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookStripeController extends AbstractController
{
    #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function checkoutSession(
        Request $request,
        StripeManager $stripeManager,
        TransactionRepository $transactionRepository,
        EntityManagerInterface $em,
        MailerManager $mailer,
        array $stripeParameters,
        LoggerInterface $stripeLogger
    ): Response {
        $endpoint_secret = $stripeParameters['wh_secret_key'];

        $payload = $request->getContent();
        $event = null;

        try {
            $event = Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException) {
            // Invalid payload
            return new Response('⚠️  Webhook error while parsing basic request.', 400);
        }

        if ($endpoint_secret) {
            // Only verify the event if there is an endpoint secret defined
            // Otherwise use the basic decoded event
            // Je récupère la signature
            $header = 'Stripe-Signature';
            $sig_header = $request->headers->get($header);

            try {
                $event = Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } catch (SignatureVerificationException) {
                // Invalid signature
                return new Response('⚠️  Webhook error while validating signature.', 400);
            }
        }

        // Handle the event
        switch ($event->type) {
            // @var User $user
            case 'payment_intent.succeeded':
                $session = $event->data->object;
                $paymentIntent = $stripeManager->retrievePaymentIntent($session->id);
                $transaction = $transactionRepository->findOneBy(['paymentIntentId' => $paymentIntent->id]);
                $transaction->setStatus(TransactionStatus::VALIDATE);
                // on fait un virement aux différents bailleurs
                $stripeManager->transferPaymentIntentToLessor($paymentIntent, $transaction);
                // mettre à jour le nombre de produits restant pour chaque
                foreach ($transaction->getTransactionLines() as $transactionLine) {
                    /**
                     * @var TransactionLine $transactionLine
                     */
                    $product = $transactionLine->getProduct();
                    $product->setQuantityAllReadyReserved(
                        $product->getQuantityAllReadyReserved() + $transactionLine->getQuantity()
                    );
                }

                $reservation = (new Reservation())
                    ->setTransaction($transaction)
                    ->setStatus(ReservationStatus::PENDING)
                    ->setAuthor($transaction->getAuthor())
                ;
                $em->persist($reservation);
                $em->flush();

                $mailer->sendMailNotification(
                    $transaction->getAuthor()->getEmail(),
                    'emails/reservation_validation.html.twig',
                    [
                        'user' => $transaction->getAuthor(),
                        'transaction' => $transaction,
                    ]
                );
                // notification du bailleur
                foreach ($transaction->getTransactionLines() as $transactionLine) {
                    $mailer->sendMailNotification(
                        $transactionLine->getProduct()->getAuthor()->getEmail(),
                        'emails/lessor_reservation_validation.html.twig',
                        [
                            'seller' => strtoupper((string) $transactionLine->getProduct()->getAuthor()->getFirstname()),
                            'buyer' => strtoupper($transaction->getAuthor()->getFullname()),
                            'title' => $transactionLine->getProduct()->getTitle(),
                            'startDate' => $transactionLine->getStartDate()->format('d/m/Y'),
                            'endDate' => $transactionLine->getEndDate()->format('d/m/Y'),
                            'quantity' => $transactionLine->getQuantity(),
                            'totalAmount' => $transactionLine->getAmountTtc() / 100,
                        ]
                    );
                }

                break;

            case 'payment_intent.canceled':
            case 'payment_intent.payment_failed':
                $session = $event->data->object;
                $paymentIntent = $stripeManager->retrievePaymentIntent($session->id);
                $transaction = $transactionRepository->findOneBy(['paymentIntentId' => $paymentIntent->id]);
                $transaction->setStatus(TransactionStatus::CANCELED);
                $em->flush();

                break;

                // Unhandled event type
            default:
                // Unexpected event type
                return new Response('non', Response::HTTP_BAD_REQUEST);
        }

        return new Response('', Response::HTTP_OK);
    }
}
