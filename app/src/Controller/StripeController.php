<?php
    
    namespace App\Controller;
    
    use App\Repository\UserRepository;
    use App\Service\StripeManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class StripeController extends AbstractController
    {
        #[Route('/souscription/{type}/{token}', name: 'app_stripe_checkout_session', requirements: ['type' => 'abo1|abo2|abo3'])]
        public function checkoutSession(
            StripeManager $stripeManager,
            Request $request,
            UserRepository $userRepository,
            string $type,
            string $token = null
        ): Response {
           
            if ($token) {
                $user = $userRepository->findOneBy(['token' => $token]);
            } else {
                $user = $this->getUser();
            }
           
            $checkoutSession = $stripeManager->createCheckoutStripe($type, $user);
           
            if ($request->isMethod("POST")) {
                
                return $this->redirect($checkoutSession->url);
            }
            
            return $this->renderForm('stripe/checkout.html.twig', [
                'checkoutSession' => $checkoutSession,
                'type' => $type,
                'token' => $user->getToken()
            ]);
        }
        
        #[Route('/gestion-abonnement', name: 'app_stripe_customer_portal_session')]
        public function handleSubcription(
            StripeManager $stripeManager,
        ): Response {
           
            return $this->redirect($stripeManager->customerPortalSession($this->getUser())->url);
        }
        
        #[Route('/success', name: 'app_stripe_success')]
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
        
        #[Route('/cancel', name: 'app_stripe_cancel')]
        public function cancel(): Response
        {
            return $this->render('stripe/cancel.html.twig');
        }
    }
