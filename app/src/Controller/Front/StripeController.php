<?php
    
    namespace App\Controller\Front;
    
    use App\Repository\UserRepository;
    use App\Service\StripeManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class StripeController extends AbstractController
    {
        #[Route('/paiement', name: 'stripe_checkout_session')]
        public function checkoutSession(
            StripeManager $stripeManager,
            Request $request,
            UserRepository $userRepository
        ): Response {
            $user = $this->getUser();

            $checkoutSession = $stripeManager->createCheckoutStripe("abo1", $user);
           
            if ($request->isMethod("POST")) {
                
                return $this->redirect($checkoutSession->url);
            }
            
            return $this->render('stripe/checkout.html.twig', [
                'checkoutSession' => $checkoutSession,
                'type' => "abo1",
                'token' => $user->getToken()
            ]);
        }
        
        #[Route('/gestion-abonnement', name: 'stripe_customer_portal_session')]
        public function handleSubcription(
            StripeManager $stripeManager,
        ): Response {
           
            return $this->redirect($stripeManager->customerPortalSession($this->getUser())->url);
        }
        
        #[Route('/success', name: 'stripe_success')]
        public function success(StripeManager $stripeManager, Request $request, UserRepository $userRepository, UserManager $userManager): Response
        {
            $sessionStripe = $stripeManager->retrieveCheckout($request->query->get('session_id'));
            $subscription = $stripeManager->retrieveSubcription($sessionStripe->subscription);
            $user = $userRepository->findOneBy(['stripeCustomerId' => $sessionStripe->customer]);
            if ($user) {
                
                return $this->render('stripe/success.html.twig', ['user' => $user]);
            }
            
            return $this->render('user/edit.html.twig', []);
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
