<?php
    
    namespace App\Controller;
    
    use App\Entity\User;
    use App\Repository\UserRepository;
    use App\Service\MailerManager;
    use App\Service\StripeManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

    class WebhookStripeController extends AbstractController
    {
        #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
        public function checkoutSession(
            Request $request,
            StripeManager $stripeManager,
            UserRepository $userRepository,
            UserManager $userManager,
            MailerManager $mailer,
            LoginLinkHandlerInterface $loginLinkHandler,
            array $stripeParameters
        ): Response {
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
                case 'checkout.session.completed':
                case 'customer.subscription.created':
                    $session = $event->data->object;
                    $subscription = $stripeManager->retrieveSubcription($session->subscription);
                    $user = $userRepository->findOneBy(['stripeCustomerId' => $session->customer]);
                    
                    if ($user) {
                        list($status, $role) = $stripeManager->getTypeAbonnement($subscription->plan->id);
                        $user->setStripePlan($status);
                        $user->setStripeStatus('active');
                        $user->setRoles([$role]);
                        $user->setNumberSpam(null);
                        $user->setLastUpdateNumberSpam(null);
                        $userManager->saveUser();
                        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
                        $mailer->sendMailNotification(
                            $user->getEmail(),
                            'emails/create_account_abonnement_login_link.html.twig',
                            [
                                'link' => $loginLinkDetails->getUrl(),
                                'user' => $user,
                                'expiresAt' => $loginLinkDetails->getExpiresAt()
                            ]
                        );
                    }
                    break;
                case 'customer.subscription.updated':
                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $user = $userRepository->findOneBy(['stripeCustomerId' => $subscription->customer]);
                    if ($user) {
                        list($status, $role) = $stripeManager->getTypeAbonnement($subscription->items->data[0]->price->id);
                        if ($subscription->cancel_at_period_end && $event->type == "customer.subscription.deleted") {
                            $user->setStripePlan(null);
                            $user->setStripeStatus(null);
                            $user->setRoles([User::ROLE_USER]);
                        } else {
                            $user->setStripePlan($status);
                            $user->setStripeStatus($subscription->status);
                            $user->setRoles([$role]);
                        }
                        $userManager->saveUser();
                        $mailer->sendMailNotification(
                            $user->getEmail(),
                            'subcription_change.html.twig',
                            [
                                'user' => $user,
                            ]
                        );
                    }
                    break;
                // Unhandled event type
                default:
                    // Unexpected event type
                    return new Response('non', Response::HTTP_BAD_REQUEST);
            }
            
            return new Response('', Response::HTTP_OK);
        }
    }
