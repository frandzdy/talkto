<?php


namespace App\Service;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\TransactionLine;
use App\Entity\User;
use App\Enum\TransactionLineStatus;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\StripeClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeManager
{
    private StripeClient $stripe;

    const ABO_1 = 'Spammi';
    const ABO_2 = 'Spammeur';
    const ABO_3 = 'Spamlover';

    /**
     * StripeManager constructor.
     */
    public function __construct(
        private UrlGeneratorInterface  $generator,
        private array                  $stripeParameters,
        private ProductRepository      $productRepository,
        private EntityManagerInterface $em
    )
    {
        $this->stripe = new StripeClient($this->stripeParameters['secret_key']);
    }

    /**
     * Création d'un utilisateur
     *
     * @param User $user
     * @return Customer|null
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCustomer(User $user): ?Customer
    {
        return $this->stripe->customers->create(
            [
                'email' => $user->getEmail(),
                'name' => $user->getLastname(),
                'metadata' => [
                    'id_client' => $user->getId()
                ]
            ]
        );
    }

    /**
     *  Création d'un compte commercial
     *
     * @param User $user
     * @return Account|null
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createAccount(User $user): ?Account
    {
        return $this->stripe->accounts->create(
            [
                'type' => 'express',
                'country' => 'FR',
                'email' => $user->getEmail(),
                'capabilities' =>
                    [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true]
                    ],
                'business_type' => 'individual',
                'default_currency' => 'EUR'
            ]
        );
    }

    /**
     * @param User $user
     * @return AccountLink
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createAccountLink(User $user): AccountLink
    {
        return $this->stripe->accountLinks->create(
            [
                'account' => $user->getStripeAccountId(),
                'refresh_url' => $this->generator->generate('front_stripe_reauth', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'return_url' => $this->generator->generate('front_stripe_return', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'type' => 'account_onboarding',
            ]
        );
    }

    /**
     * @param User $user
     * @return Account
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function retrieveAccount(User $user): Account
    {
        return $this->stripe->accounts->retrieve($user->getStripeAccountId());
    }

    /**
     * Retourne un utilisateur
     */
    public function retrieveCustomer($customerId): ?Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    /**
     * retourne un checkout
     */
    public function retrieveCheckout(string $checkoutId): ?Session
    {
        return $this->stripe->checkout->sessions->retrieve($checkoutId);
    }

    /**
     * Création du paiement
     */
    public function createPaymentIntent(array $cart, User $user, Transaction $transaction): PaymentIntent
    {
        if (!$user->getId()) {
            $customerId = $this->createCustomer($user)->id;
        } else {
            $customerId = $user->getStripeCustomerId();
        }

        return $this->stripe->paymentIntents->create(
            [
                'amount' => $cart['totalAmount'] * 100,
                'customer' => $customerId, // $customerId
                'currency' => 'eur',
                'setup_future_usage' => 'off_session',
                'automatic_payment_methods' => ['enabled' => true],
                'transfer_group' => $transaction->getReference()
            ]
        );
    }

    /**
     * @param PaymentIntent $paymentIntent
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function captureAndTransferPaymentIntent(PaymentIntent $paymentIntent, Transaction $transaction): array
    {
        foreach ($transaction->getTransactionLines() as $transactionLine) {
            /**
             * @var TransactionLine $transactionLine
             */
            $product = $transactionLine->getProduct();
            $renter = $transactionLine->getProduct()->getAuthor();
            $numberDays = $transactionLine->getStartDate()->diff($transactionLine->getEndDate())->days === 0
                ? 1
                : $transactionLine->getStartDate()->diff($transactionLine->getEndDate())->days;
            $transfer = $this->stripe->transfers->create(
                [
                    'amount' => ((int)$product->getAmount()
                                * (int)$transactionLine->getQuantity()
                                * (int)$numberDays) * 100,
                    'currency' => 'eur',
                    'destination' => $renter->getStripeAccountId(),//'acct_1NC9n5FZz11Scp6n'
                    'source_transaction' => $paymentIntent->charges->first()->id,
                    'transfer_group' => $transaction->getReference(),
                ]
            );
        }

        return [$transfer];
    }

    /**
     * @param $products
     * @param Transaction $transaction
     * @return void
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
            if (str_contains($cart['flatpickrDate'], 'au')) {
                $reservationDate = explode(' au ', $cart['flatpickrDate']);
            } else {
                $reservationDate = [
                    0 => $cart['flatpickrDate'],
                    1 => $cart['flatpickrDate']
                ];
            }

            if ($product && array_key_exists(0, $reservationDate)) {
                $amountTtc = $cart['price'] * $cart['quantity'] * $cart['numberDays'];
                $amountTva = $amountTtc * 0.2;
                $amountFees = $amountTtc * 0.1;
                $transactionLine = (new TransactionLine())
                    ->setToken(hash('sha256', random_bytes(32)))
                    ->setTransaction($transaction)
                    ->setProduct($product)
                    ->setQuantity($cart['quantity'])
                    ->setAmountTtc($amountTtc * 100)
                    ->setAmountTva($amountTva * 100)
                    ->setFees($amountFees * 100)
                    ->setStartDate(new \DateTime($reservationDate[0]))
                    ->setEndDate(new \DateTime($reservationDate[1]))
                    ->setStatus(TransactionLineStatus::WAITING);
                $transaction->addTransactionLine($transactionLine);
                $response['transactionTotalTtc'] += $amountTtc;
                $response['transactionTotalTva'] += $amountTva;
                $response['transactionTotalFees'] += $amountFees;
            }
        }

        $transaction->setTotalAmountTtc($response['transactionTotalTtc'] * 100)
        ->setTotalAmountTva($response['transactionTotalTva'] * 100)
        ->setTotalFees($response['transactionTotalFees'] * 100);

        return $response;
    }

    /**
     * @param string $paymenIntentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $paymenIntentId): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymenIntentId);
    }

    /**
     * @param string $paymenIntentId
     * @return PaymentIntent
     */
    public function updatePaymentIntent(string $paymenIntentId, array $carts): PaymentIntent
    {
        return $this->stripe->paymentIntents->update(
            $paymenIntentId,
            [
                'amount' => $carts['totalAmount']
            ]
        );
    }

    /**
     * @param TransactionLine $transactionLine
     * @return Refund
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function refundTransactionLine(TransactionLine $transactionLine): Refund
    {
        return $this->stripe->refunds->create(
            [
                'payment_intent' => $transactionLine->getTransaction()->getPaymentIntentId(),
                'amount' => $transactionLine->getAmountTtc() - $transactionLine->getFees(),
            ]
        );
    }
}
