<?php
    
    namespace App\Controller\Front;
    
    use App\Entity\User;
    use App\Form\LoginType;
    use App\Form\UserPaymentType;
    use App\Form\UserType;
    use App\Repository\UserRepository;
    use App\Service\StripeManager;
    use App\Service\UserManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    class StripeController extends AbstractController
    {
        #[Route('/paiement', name: 'stripe_payment_intent', methods: ['GET'])]
        public function paymentIntent(SessionInterface $session, StripeManager $stripeManager): Response
        {
            /**
             * on récupère l'utilisateur connecté
             * si pas connecté alors on crée un compte stripe avec les informations de facturation
             */
            $carts = $session->get('cart', null);

            if (!$carts) {
                return $this->redirectToRoute('front_home');
            }

            if (!isset($carts['paymentIntentId'])) {
                $paymentIntent = $stripeManager->createPaymentIntent($carts);
                $carts['paymentIntentId'] = $paymentIntent->id;
            } else {
                $paymentIntent = $stripeManager->retrievePaymentIntent($carts['paymentIntentId']);
            }
            $session->set('cart', $carts);
            $clientSecret = $paymentIntent->client_secret;

            return $this->render('front/stripe/checkout.html.twig', compact('carts', 'clientSecret'));
        }

        #[Route('/paiement-connexion', name: 'stripe_payment_user_login', options: ['expose' => true] , methods: ['GET'])]
        public function paymentUserLogin(UserManager $userManager, Request $request, AuthenticationUtils $authenticationUtils): Response
        {
            $user = $userManager->createUser();
            $form = $this->createForm(LoginType::class, $user);

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
                return $this->json(['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]);
            }

            return $this->render(
                'front/auth/_form.html.twig',
                [
                    'form' => $form,
                    'last_username' => $lastUsername,
                    'error' => $error
                ]
            );
        }

        #[Route('/paiement-user-creation', name: 'stripe_payment_user_create', options: ['expose' => true], methods: ['GET'])]
        public function checkPaymentUserCreate(SessionInterface $session, UserManager $userManager, StripeManager $stripeManager, Request $request): Response
        {
            $user = $userManager->createUser();
            $form = $this->createForm(UserPaymentType::class, $user);

            if ($form->handleRequest()->isSubmitted() && $form->isValid()) {
                return $this->json(['success' => 'true', 'redirectUrl' => $this->generateUrl('front_stripe_payment_intent')]);
            }

            return $this->render('front/stripe/_form_user.html.twig', ['form' => $form]);
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
