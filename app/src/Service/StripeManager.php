<?php


namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Product;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\Transfer;
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
        private UrlGeneratorInterface $generator,
        private array                 $stripeParameters
    )
    {
        $this->stripe = new StripeClient($this->stripeParameters['secret_key']);
    }

    /**
     * Retourne une instance stripe
     */
    public function getStripeInstance()
    {
        return $this->stripe;
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
            $customerId = $this->createCustomer($user);
        } else {
            $customerId = $user->getStripeCustomerId();
        }

        return $this->stripe->paymentIntents->create(
            [
                'amount' => $cart['totalAmount'] * 100,
                'customer' => 'cus_NyIudynRPUwh2I', // $customerId
                'currency' => 'eur',
                'setup_future_usage'=> 'on_session',
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
        echo '<pre>';
        dump($transaction);
        echo '</pre>';
        echo 'Répertoire : ' . __DIR__ . ' Ligne : ' . __LINE__ . ' Méthode : ' . __METHOD__ . ' Debug Frandzdy';
        die;
        $transfer = $this->stripe->transfers->create(
            [
                'amount' => 8000,
                'currency' => 'eur',
                'destination' => 'acct_1NC9n5FZz11Scp6n',
                'source_transaction' => $paymentIntent->charges->first()->id,
                'transfer_group' => 'ORDER10',
            ]
        );

        return [$transfer];
    }

    /**
     * @param string $paymenIntentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $paymenIntentId): PaymentIntent
    {
        return $this->stripe->paymentIntents->retrieve($paymenIntentId);
    }
}
