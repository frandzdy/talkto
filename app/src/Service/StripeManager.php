<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\TransactionLineStatus;
use App\Enum\TransactionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\LoginLink;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\StripeClient;
use Stripe\TransferReversal;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class StripeManager
{
    private StripeClient $stripe;

    /**
     * StripeManager constructor.
     */
    public function __construct(
        private UrlGeneratorInterface $generator,
        private array $stripeParameters,
        private EntityManagerInterface $em
    ) {
        $this->stripe = new StripeClient(
            [
                'api_key' => $this->stripeParameters['secret_key'],
                'stripe_version' => '2024-04-10',
            ]
        );
    }

    /**
     * Création d'un utilisateur.
     *
     * @throws ApiErrorException
     */
    public function createCustomer(User $user): ?Customer
    {
        return $this->stripe->customers->create(
            [
                'email' => $user->getEmail(),
                'name' => $user->getLastname(),
                'metadata' => [
                    'id_client' => $user->getId(),
                ],
            ]
        );
    }

    /**
     *  Création d'un compte commercial.
     *
     * @throws ApiErrorException
     */
    public function createAccount(User $user): ?Account
    {
        return $this->stripe->accounts->create(
            [
                'type' => 'express',
                'country' => 'FR',
                'email' => $user->getEmail(),
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
                'default_currency' => 'EUR',
            ]
        );
    }

    /**
     * @throws ApiErrorException
     */
    public function createAccountLink(User $user): AccountLink
    {
        return $this->stripe->accountLinks->create(
            [
                'account' => $user->getStripeAccountId(),
                'refresh_url' => $this->generator->generate(
                    'front_stripe_reauth',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'return_url' => $this->generator->generate(
                    'front_stripe_return',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'type' => 'account_onboarding',
            ]
        );
    }

    /**
     * Retourne un compte connecté Stripe.
     */
    public function retrieveAccount(User $user): Account
    {
        return $this->stripe->accounts->retrieve($user->getStripeAccountId());
    }

    /**
     * Retourne un utilisateur client Stripe.
     */
    public function retrieveCustomer(string $customerId): ?Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    /**
     * retourne un checkout.
     */
    public function retrieveCheckout(string $checkoutId): ?Session
    {
        return $this->stripe->checkout->sessions->retrieve($checkoutId);
    }

    /**
     * Création du paiement.
     */
    public function createPaymentIntent(array $cart, User $user, Transaction $transaction): PaymentIntent
    {
        $customerId = $user->getId() ? $user->getStripeCustomerId() : $this->createCustomer($user)->id;

        return $this->stripe->paymentIntents->create(
            [
                'amount' => $cart['totalAmount'] * 100,
                'customer' => $customerId,
                'currency' => 'eur',
                'setup_future_usage' => 'off_session',
                'transfer_group' => $transaction->getReference(),
                'receipt_email' => $user->getEmail(), // 'latest_charge' => 'expand',
                'description' => vsprintf('Réf reented : %s', [$transaction->getReference()]),
            ]
        );
    }

    /**
     * Transfert l'argent vers les différents bailleurs après la validation du paiement.
     */
    public function transferPaymentIntentToLessor(PaymentIntent $paymentIntent, Transaction $transaction): void
    {
        foreach ($transaction->getTransactionLines() as $transactionLine) {
            /**
             * @var TransactionLine $transactionLine
             */
            $product = $transactionLine->getProduct();
            $renter = $transactionLine->getProduct()->getAuthor();
            $numberDays = 0 === $transactionLine->getStartDate()->diff($transactionLine->getEndDate())->days
                ? 1
                : $transactionLine->getStartDate()->diff($transactionLine->getEndDate())->days;
            $transfer = $this->stripe->transfers->create(
                [
                    'amount' => ((int) $product->getAmount()
                            * (int) $transactionLine->getQuantity()
                            * (int) $numberDays) * 100,
                    'currency' => 'eur',
                    'destination' => $renter->getStripeAccountId(),
                    'source_transaction' => $paymentIntent->latest_charge,
                    'transfer_group' => $transaction->getReference(),
                    'metadata' => [
                        'product' => $product->getId(),
                        'productName' => $product->getTitle(),
                        'startDate' => $transactionLine->getStartDate()->format('Y-m-d'),
                        'endDate' => $transactionLine->getEndDate()->format('Y-m-d'),
                        'quantity' => $transactionLine->getQuantity(),
                    ],
                ]
            );
            $transactionLine->setTransfertId($transfer->id);
            //  on prend la caution sur chaque paiement
            //            $cautionIntent = $this->stripe->paymentIntents->create(
            //                [
            //                    'amount' => (int)$product->getCaution() * 100,
            //                    'customer' => $transaction->getAuthor()->getStripeCustomerId(), // $customerId
            //                    'currency' => 'eur',
            //                    'automatic_payment_methods' => ['enabled' => true],
            //                    'expand' => ['latest_charge'],
            //                    'payment_method_options' =>
            //                        [
            //                            'card' =>
            //                                [
            //                                    'capture_method' => 'manuel',
            //                                    'request_extended_authorization' => 'if_available'
            //                                ]
            //                        ],
            //                    'confirm' => true,
            //                    'receipt_email' => $transaction->getAuthor()->getEmail(),
            //                    'description' => sprintf('Caution Reented : Ref %s', $transaction->getReference())
            //                ]
            //            );
            //            $transactionLine->setCautionId($cautionIntent->id);
        }
    }

    /**
     * Ajout ou mets à jour une ligne de transaction.
     *
     * @param mixed $products
     */
    public function addOrUpdateTransactionLine($products, Transaction $transaction): array
    {
        $response = [
            'transactionTotalTtc' => 0,
            'transactionTotalTva' => 0,
            'transactionTotalFees' => 0,
        ];

        foreach ($products as $token => $cart) {
            $product = $this->em->getRepository(Product::class)->findOneBy(['token' => $token]);
            if (str_contains((string) $cart['flatpickrDate'], 'au')) {
                $reservationDate = explode(' au ', (string) $cart['flatpickrDate']);
            } else {
                $reservationDate = [
                    0 => $cart['flatpickrDate'],
                    1 => $cart['flatpickrDate'],
                ];
            }

            if ($product && array_key_exists(0, $reservationDate)) {
                $amountTtc = $cart['price'] * $cart['quantity'] * $cart['numberDays'];
                $amountTva = $amountTtc * 0.2;
                $amountFees = $amountTtc * 0.1;
                $transactionLine = (new TransactionLine())
                    ->setTransaction($transaction)
                    ->setProduct($product)
                    ->setQuantity($cart['quantity'])
                    ->setAmountTtc($amountTtc * 100)
                    ->setAmountTva($amountTva * 100)
                    ->setFees($amountFees * 100)
                    ->setStartDate(new \DateTime($reservationDate[0]))
                    ->setEndDate(new \DateTime($reservationDate[1]))
                    ->setStatus(TransactionLineStatus::WAITING)
                ;
                $transaction->addTransactionLine($transactionLine);
                $response['transactionTotalTtc'] += $amountTtc;
                $response['transactionTotalTva'] += $amountTva;
                $response['transactionTotalFees'] += $amountFees;
            }
        }

        $transaction->setTotalAmountTtc($response['transactionTotalTtc'] * 100)
            ->setTotalAmountTva($response['transactionTotalTva'] * 100)
            ->setTotalFees($response['transactionTotalFees'] * 100)
        ;

        return $response;
    }

    /**
     * Retourne un paiement.
     */
    public function retrievePaymentIntent(string $paymenIntentId): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymenIntentId);
    }

    /**
     * Retourne un paiement.
     */
    public function retrieveCharge(?string $chargeId = null): ?Charge
    {
        if ($chargeId) {
            return $this->stripe->charges->retrieve($chargeId);
        }

        return null;
    }

    /**
     * Effectue un remboursement complèt pour une ligne de transaction.
     */
    public function refundTransactionLine(TransactionLine $transactionLine): Refund
    {
        // On annule le transfert effectué au compte bailleur
        $transfertReversal = $this->cancelTranfert(
            $transactionLine->getTransfertId(),
            $transactionLine->getAmountTtc() - $transactionLine->getFees()
        );
        $transactionLine->setCancelTransfertId($transfertReversal->id)
            ->setCancelAmount($transactionLine->getAmountTtc())
        ;

        // On annule le paiement effectué par le locataire
        return $this->stripe->refunds->create(
            [
                'payment_intent' => $transactionLine->getTransaction()->getPaymentIntentId(),
                'amount' => $transactionLine->getAmountTtc(),
            ]
        );
    }

    /**
     * Créer une transaction avec les informations du panier.
     */
    public function createTransaction(array &$carts, User $user): array
    {
        if (!$carts['transactionId']) {
            $transaction = (new Transaction())
                ->setStatus(TransactionStatus::WAITING)
                ->setAuthor($user)
            ;
            $this->addOrUpdateTransactionLine($carts['products'], $transaction);
            $this->em->persist($transaction);
            $this->em->flush();
            $transaction->setReference(
                sprintf('#REF_%s', str_pad((string) $transaction->getId(), 6, '0', STR_PAD_LEFT))
            );
            $paymentIntent = $this->createPaymentIntent($carts, $user, $transaction);
            $transaction->setPaymentIntentId($paymentIntent->id);
            $this->em->flush();
            $carts['transactionId'] = $transaction->getId();
        } else {
            $transaction = $this->em->getRepository(Transaction::class)->findOneBy(['id' => $carts['transactionId']]);

            foreach ($transaction->getTransactionLines() as $transactionLine) {
                $transaction->removeTransactionLine($transactionLine);
                $this->em->remove($transactionLine);
            }

            $this->em->flush();

            // mettre à jour aussi la transaction
            $this->addOrUpdateTransactionLine($carts['products'], $transaction);
            $paymentIntent = $this->createPaymentIntent($carts, $user, $transaction);
            $transaction->setPaymentIntentId($paymentIntent->id);

            $this->em->flush();
        }

        return [$paymentIntent, $transaction];
    }

    /**
     * Annule un transfert vers un compte connecté partiellement ou complèt.
     *
     * @param mixed $amount
     */
    public function cancelTranfert(string $transfertId, $amount): TransferReversal
    {
        return $this->stripe->transfers->createReversal(
            $transfertId,
            ['amount' => $amount * 100]
        );
    }

    /**
     * Annule un transfert vers un compte connecté partiellement ou complèt.
     *
     * @param mixed $amount
     */
    public function caution(TransactionLine $transactionLine, $amount): PaymentIntent
    {
        $intent = $this->retrievePaymentIntent($transactionLine->getCautionId());

        if ('requires_capture' === $intent->status) {
            $intent->capture(
                [
                    'amount_to_capture' => $amount * 100,
                ]
            );
            if ('succeeded' === $intent->status) {
                $transfer = $this->stripe->transfers->create(
                    [
                        'amount' => $amount * 100,
                        'currency' => 'eur',
                        'destination' => $transactionLine->getProduct()->getAuthor()->getStripeAccountId(),
                        'source_transaction' => $intent->latest_charge,
                        'transfer_group' => $transactionLine->getTransaction()->getReference(),
                    ]
                );
                $transactionLine->setCaptureCautionId($intent->id);
            }
        }

        return $intent;
    }

    /**
     * Retourne le lien de la facture.
     */
    public function getInvoice(Transaction $transaction): ?string
    {
        return $this->retrieveCharge(
            $this->retrievePaymentIntent($transaction->getPaymentIntentId())?->latest_charge
        )?->receipt_url;
    }

    /**
     * Retourne le lien vers le compte client STRIPE.
     */
    public function getAccountLink(User $lessor): LoginLink
    {
        return $this->stripe->accounts->createLoginLink($lessor->getStripeAccountId());
    }
}
