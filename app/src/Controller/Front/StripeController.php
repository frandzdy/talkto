<?php
    
    namespace App\Controller\Front;
    
    use App\Repository\UserRepository;
    use App\Service\StripeManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class StripeController extends AbstractController
    {
        #[Route('/paiement', name: 'stripe_payment_intent', methods: ['GET'])]
        public function paymentIntent(SessionInterface $session, StripeManager $stripeManager): Response
        {
            $carts = $session->get('cart', null);
            $paymentIntent = $stripeManager->createPaymentIntent();
            $clientSecret = $paymentIntent->client_secret;

            return $this->render('front/stripe/checkout.html.twig', compact('carts', 'clientSecret'));
        }

        #[Route('/success', name: 'stripe_success', options: ['expose' => true], methods: ['POST', 'GET'])]
        public function success(StripeManager $stripeManager, Request $request, UserRepository $userRepository, UserManager $userManager): Response
        {

            $paymentIntent = $stripeManager->retrievePaymentIntent($request->query->get('payment_intent'));
            $paymentIntent = $stripeManager->captureAndTransferPaymentIntent($paymentIntent);
            echo '<pre>';
            dump($paymentIntent);
            echo '</pre>';
            echo 'Répertoire : ' . __DIR__ . ' Ligne : ' . __LINE__ . ' Méthode : ' . __METHOD__ . ' Debug Frandzdy';
            die;
//            //$subscription = $stripeManager->retrieveSubcription($sessionStripe->subscription);
//            $user = $userRepository->findOneBy(['stripeCustomerId' => $sessionStripe->customer]);
//            if ($user) {
//
//                return $this->render('front/stripe/success.html.twig', ['user' => $user]);
//            }
            
            return $this->redirectToRoute('front_user_account');
        }
        
        #[Route('/cancel', name: 'stripe_cancel')]
        public function cancel(): Response
        {
            return $this->render('stripe/cancel.html.twig');
        }

        #[Route('/reauth', name: 'stripe_reauth')]
        #[IsGranted("IS_AUTHENTICATED_FULLY")]
        public function reauth(StripeManager $stripeManager, Request $request, UserRepository $userRepository): Response
        {
            return $this->redirect($stripeManager->createAccountLink($this->getUser())->url);
        }

        #[Route('/return', name: 'stripe_return')]
        #[IsGranted("IS_AUTHENTICATED_FULLY")]
        public function return(StripeManager $stripeManager): Response
        {
            $user = $this->getUser();
            $account = $stripeManager->retrieveAccount($user);

            if ($account->details_submitted || $account->charges_enabled) {
                $user->setIsStripeAccountActive(true);
            }

            return $this->redirectToRoute('front_user_account');
        }
    }
