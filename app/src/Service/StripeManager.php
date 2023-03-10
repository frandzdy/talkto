<?php
    
    
    namespace App\Service;
    
    use App\Entity\User;
    use Stripe\Checkout\Session;
    use Stripe\Customer;
    use Stripe\Product;
    use Stripe\StripeClient;
    use Stripe\Subscription;
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
            private array $stripeParameters
        ) {
            $this->stripe = new StripeClient($this->stripeParameters['secret_key']);
        }
    
        /**
         * Retourne une instance stripe
         */
        public function getStripeInstance(){
            return $this->stripe;
        }
    
        /**
         * Retourne le nom de l'abonnement et du rôle ayant été souscrit
         */
        public function getTypeAbonnement(string $priceId): array
        {
            switch ($priceId) {
                case $this->stripeParameters['prices']['price_abo1']:
                    $res = [self::ABO_1, User::ROLE_ABO_1];
                    break;
                case $this->stripeParameters['prices']['price_abo2']:
                    $res = [self::ABO_2, User::ROLE_ABO_2];
                    break;
                case $this->stripeParameters['prices']['price_abo3']:
                    $res = [self::ABO_3, User::ROLE_ABO_3];
                    break;
                default:
                    $res = [];
                    break;
            }
            
            return $res;
        }
        
        /**
         * Création d'un utilisateur
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
         * retourne un checkout
         */
        public function retrieveSubcription(string $subscritionId): ?Subscription
        {
            return $this->stripe->subscriptions->retrieve($subscritionId);
        }
    
        /**
         * retourne un checkout
         */
        public function retrieveProduct(string $productId): ?Product
        {
            return $this->stripe->products->retrieve($productId);
        }
        /**
         * retourne un checkout
         */
        public function customerPortalSession(User $user): ?\Stripe\BillingPortal\Session
        {
            return $this->stripe->billingPortal->sessions->create(
                [
                    'customer' => $user->getStripeCustomerId(),
                    'return_url' => $this->generator->generate('user_edit', [], UrlGeneratorInterface::ABSOLUTE_URL)
                ]
            );
        }
        
        /**
         * Création du paiement pour un abonnement
         */
        public function createCheckoutStripe(string $type, User $user)
        {
            return $this->stripe->checkout->sessions->create(
                [
                    'customer' => $user->getStripeCustomerId(),
                    'line_items' => [
                        [
                            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                            'price' => $this->stripeParameters["prices"]["price_$type"],
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'subscription',
                    'success_url' => $this->generator->generate('app_stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL).'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => $this->generator->generate('app_stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            );
        }
    }
